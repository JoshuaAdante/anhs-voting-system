import { useQuery } from "@tanstack/react-query";
import { useMemo, useRef, useState } from "react";
import { toast } from "sonner";
import { Plus, Trash2, RotateCcw, Upload, Search } from "lucide-react";

import { AdminLayout } from "@/components/AdminLayout";
import { supabase } from "@/integrations/supabase/client";
import { GRADES, studentFullName } from "@/lib/brand";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";

const emptyForm = {
  lrn: "",
  last_name: "",
  first_name: "",
  given_name: "",
  grade: GRADES[0]!,
  section: "",
  sex: "Male",
};

export function StudentsPage() {
  const [search, setSearch] = useState("");
  const [grade, setGrade] = useState("all");
  const [section, setSection] = useState("all");
  const [status, setStatus] = useState("all");
  const [form, setForm] = useState(emptyForm);
  const [open, setOpen] = useState(false);
  const fileRef = useRef<HTMLInputElement>(null);

  const { data: students = [], refetch } = useQuery({
    queryKey: ["students"],
    queryFn: async () => {
      const { data, error } = await supabase
        .from("students")
        .select("*")
        .order("last_name");
      if (error) throw error;
      return data;
    },
  });

  const sections = useMemo(
    () => Array.from(new Set(students.map((s) => s.section))).sort(),
    [students],
  );

  const filtered = students.filter((s) => {
    const name = studentFullName(s).toLowerCase();
    if (search && !name.includes(search.toLowerCase()) && !s.lrn.includes(search)) return false;
    if (grade !== "all" && s.grade !== grade) return false;
    if (section !== "all" && s.section !== section) return false;
    if (status === "voted" && !s.has_voted) return false;
    if (status === "not" && s.has_voted) return false;
    return true;
  });

  async function addStudent() {
    if (!form.lrn || !form.last_name || !form.first_name || !form.section) {
      toast.error("LRN, last name, first name and section are required.");
      return;
    }
    const { error } = await supabase.from("students").insert({
      ...form,
      given_name: form.given_name || null,
    });
    if (error) { toast.error(error.message); return; }
    toast.success("Student added.");
    setForm(emptyForm);
    setOpen(false);
    refetch();
  }

  async function removeStudent(id: string) {
    const { error } = await supabase.from("students").delete().eq("id", id);
    if (error) { toast.error(error.message); return; }
    refetch();
  }

  async function deleteAll() {
    if (!confirm("Delete ALL students? This cannot be undone.")) return;
    const { error } = await supabase.from("students").delete().not("id", "is", null);
    if (error) { toast.error(error.message); return; }
    toast.success("All students deleted.");
    refetch();
  }

  async function resetAll() {
    if (!confirm("Reset voted status of all students?")) return;
    const { error } = await supabase
      .from("students")
      .update({ has_voted: false, voted_at: null })
      .not("id", "is", null);
    if (error) { toast.error(error.message); return; }
    toast.success("All students reset to not voted.");
    refetch();
  }

  async function uploadCsv(file: File) {
    const text = await file.text();
    const lines = text.split(/\r?\n/).filter((l) => l.trim());
    const rows: Record<string, string>[] = [];
    for (const line of lines) {
      const [lrn, last_name, first_name, given_name, g, sec, sex] = line
        .split(",")
        .map((c) => c.trim());
      if (!lrn || lrn.toLowerCase() === "lrn" || !last_name || !first_name) continue;
      rows.push({
        lrn,
        last_name,
        first_name,
        given_name: given_name || "",
        grade: g || GRADES[0]!,
        section: sec || "N/A",
        sex: sex || "Male",
      });
    }
    if (rows.length === 0) { toast.error("No valid rows found in the file."); return; }
    const { error } = await supabase.from("students").insert(
      rows.map((r) => ({ ...r, given_name: r["given_name"] || null })) as never,
    );
    if (error) { toast.error(error.message); return; }
    toast.success(`${rows.length} students uploaded.`);
    refetch();
  }

  return (
    <AdminLayout title="Student Management" subtitle="Add, filter and manage registered learners">
      <div className="flex flex-wrap items-center gap-2">
        <Dialog open={open} onOpenChange={setOpen}>
          <DialogTrigger asChild>
            <Button>
              <Plus className="mr-2 h-4 w-4" /> Add Student
            </Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Add student</DialogTitle>
            </DialogHeader>
            <div className="grid gap-4 sm:grid-cols-2">
              <div className="space-y-2 sm:col-span-2">
                <Label>LRN</Label>
                <Input
                  value={form.lrn}
                  onChange={(e) => setForm({ ...form, lrn: e.target.value })}
                />
              </div>
              <div className="space-y-2">
                <Label>Last name</Label>
                <Input
                  value={form.last_name}
                  onChange={(e) => setForm({ ...form, last_name: e.target.value })}
                />
              </div>
              <div className="space-y-2">
                <Label>First name</Label>
                <Input
                  value={form.first_name}
                  onChange={(e) => setForm({ ...form, first_name: e.target.value })}
                />
              </div>
              <div className="space-y-2 sm:col-span-2">
                <Label>Given / middle name (optional)</Label>
                <Input
                  value={form.given_name}
                  onChange={(e) => setForm({ ...form, given_name: e.target.value })}
                />
              </div>
              <div className="space-y-2">
                <Label>Grade</Label>
                <Select value={form.grade} onValueChange={(v) => setForm({ ...form, grade: v })}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {GRADES.map((g) => (
                      <SelectItem key={g} value={g}>
                        {g}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div className="space-y-2">
                <Label>Section</Label>
                <Input
                  value={form.section}
                  onChange={(e) => setForm({ ...form, section: e.target.value })}
                />
              </div>
              <div className="space-y-2">
                <Label>Sex</Label>
                <Select value={form.sex} onValueChange={(v) => setForm({ ...form, sex: v })}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="Male">Male</SelectItem>
                    <SelectItem value="Female">Female</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            </div>
            <DialogFooter>
              <Button onClick={addStudent}>Save student</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>

        <Button variant="secondary" onClick={() => fileRef.current?.click()}>
          <Upload className="mr-2 h-4 w-4" /> Upload Students
        </Button>
        <input
          ref={fileRef}
          type="file"
          accept=".csv,text/csv,text/plain"
          className="hidden"
          onChange={(e) => {
            const f = e.target.files?.[0];
            if (f) uploadCsv(f);
            e.target.value = "";
          }}
        />
        <Button variant="outline" onClick={resetAll}>
          <RotateCcw className="mr-2 h-4 w-4" /> Reset All
        </Button>
        <Button variant="destructive" onClick={deleteAll}>
          <Trash2 className="mr-2 h-4 w-4" /> Delete All
        </Button>
      </div>

      <p className="mt-2 text-xs text-muted-foreground">
        CSV format: LRN, Last name, First name, Given name, Grade, Section, Sex
      </p>

      <Card className="mt-5 card-shadow">
        <CardContent className="p-5">
          <div className="grid gap-3 md:grid-cols-4">
            <div className="relative">
              <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
              <Input
                className="pl-9"
                placeholder="Search name or LRN"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
            <Select value={grade} onValueChange={setGrade}>
              <SelectTrigger>
                <SelectValue placeholder="Grade" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All grades</SelectItem>
                {GRADES.map((g) => (
                  <SelectItem key={g} value={g}>
                    {g}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            <Select value={section} onValueChange={setSection}>
              <SelectTrigger>
                <SelectValue placeholder="Section" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All sections</SelectItem>
                {sections.map((s) => (
                  <SelectItem key={s} value={s}>
                    {s}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            <Select value={status} onValueChange={setStatus}>
              <SelectTrigger>
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="all">All learners</SelectItem>
                <SelectItem value="voted">Voted</SelectItem>
                <SelectItem value="not">Not voted</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div className="mt-5 overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>LRN</TableHead>
                  <TableHead>Name</TableHead>
                  <TableHead>Grade</TableHead>
                  <TableHead>Section</TableHead>
                  <TableHead>Sex</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead />
                </TableRow>
              </TableHeader>
              <TableBody>
                {filtered.map((s) => (
                  <TableRow key={s.id}>
                    <TableCell className="font-mono text-xs">{s.lrn}</TableCell>
                    <TableCell className="font-medium">{studentFullName(s)}</TableCell>
                    <TableCell>{s.grade}</TableCell>
                    <TableCell>{s.section}</TableCell>
                    <TableCell>{s.sex}</TableCell>
                    <TableCell>
                      <Badge variant={s.has_voted ? "default" : "secondary"}>
                        {s.has_voted ? "Voted" : "Not voted"}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-right">
                      <Button variant="ghost" size="icon" onClick={() => removeStudent(s.id)}>
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
                {filtered.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={7} className="py-10 text-center text-muted-foreground">
                      No students found.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </div>
          <p className="mt-3 text-sm text-muted-foreground">
            Showing {filtered.length} of {students.length} learners
          </p>
        </CardContent>
      </Card>
    </AdminLayout>
  );
}