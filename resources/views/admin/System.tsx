import { useQuery } from "@tanstack/react-query";
import { useState } from "react";
import { toast } from "sonner";
import { KeyRound, Printer, Trash2 } from "lucide-react";

import { AdminLayout } from "@/components/AdminLayout";
import { supabase } from "@/integrations/supabase/client";
import { GRADES, studentFullName } from "@/lib/brand";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
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

const ALPHABET = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";

function makeCode() {
  let out = "";
  const bytes = crypto.getRandomValues(new Uint8Array(8));
  for (const b of bytes) out += ALPHABET[b % ALPHABET.length];
  return out;
}

export function SystemPage() {
  const [mode, setMode] = useState<"name" | "grade">("name");
  const [studentId, setStudentId] = useState("");
  const [grade, setGrade] = useState(GRADES[0]!);
  const [search, setSearch] = useState("");
  const [busy, setBusy] = useState(false);

  const studentsQ = useQuery({
    queryKey: ["students"],
    queryFn: async () => {
      const { data, error } = await supabase.from("students").select("*").order("last_name");
      if (error) throw error;
      return data;
    },
  });

  const tokensQ = useQuery({
    queryKey: ["tokens"],
    queryFn: async () => {
      const { data, error } = await supabase
        .from("voting_tokens")
        .select("*")
        .order("created_at", { ascending: false });
      if (error) throw error;
      return data;
    },
  });

  const students = studentsQ.data ?? [];
  const tokens = tokensQ.data ?? [];
  const nameById = new Map(students.map((s) => [s.id, studentFullName(s)]));

  const matches = students.filter((s) =>
    studentFullName(s).toLowerCase().includes(search.toLowerCase()),
  );

  async function generate() {
    setBusy(true);
    try {
      let rows: { code: string; student_id: string | null; grade: string | null }[] = [];
      if (mode === "name") {
        if (!studentId) {
          toast.error("Pick a learner first.");
          return;
        }
        const s = students.find((x) => x.id === studentId);
        rows = [{ code: makeCode(), student_id: studentId, grade: s?.grade ?? null }];
      } else {
        const targets = students.filter((s) => s.grade === grade);
        if (targets.length === 0) {
          toast.error(`No learners in ${grade}.`);
          return;
        }
        const existing = new Set(
          tokens.filter((t) => !t.used && t.student_id).map((t) => t.student_id),
        );
        rows = targets
          .filter((s) => !existing.has(s.id))
          .map((s) => ({ code: makeCode(), student_id: s.id, grade: s.grade }));
        if (rows.length === 0) {
          toast.error("Every learner in this grade already has an unused token.");
          return;
        }
      }
      const { error } = await supabase.from("voting_tokens").insert(rows);
      if (error) {
        toast.error(error.message);
        return;
      }
      toast.success(`${rows.length} token(s) generated.`);
      tokensQ.refetch();
    } finally {
      setBusy(false);
    }
  }

  async function deleteToken(id: string) {
    const { error } = await supabase.from("voting_tokens").delete().eq("id", id);
    if (error) {
      toast.error(error.message);
      return;
    }
    tokensQ.refetch();
  }

  async function deleteUnused() {
    if (!confirm("Delete all unused tokens?")) return;
    const { error } = await supabase.from("voting_tokens").delete().eq("used", false);
    if (error) {
      toast.error(error.message);
      return;
    }
    toast.success("Unused tokens deleted.");
    tokensQ.refetch();
  }

  return (
    <AdminLayout
      title="System Management"
      subtitle="Generate one-time voting tokens — each token can only be used once"
    >
      <div className="grid gap-5 lg:grid-cols-[380px_1fr]">
        <Card className="card-shadow">
          <CardHeader>
            <CardTitle className="flex items-center gap-2 text-base">
              <KeyRound className="h-4 w-4" /> Generate tokens
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label>Generate by</Label>
              <Select value={mode} onValueChange={(v) => setMode(v as "name" | "grade")}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="name">By learner name</SelectItem>
                  <SelectItem value="grade">By grade level</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {mode === "name" ? (
              <div className="space-y-2">
                <Label>Search learner</Label>
                <Input
                  placeholder="Type a name"
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                />
                <div className="max-h-56 space-y-1 overflow-y-auto rounded-lg border border-border p-1">
                  {matches.slice(0, 50).map((s) => (
                    <button
                      key={s.id}
                      type="button"
                      onClick={() => setStudentId(s.id)}
                      className={`w-full rounded-md px-3 py-2 text-left text-sm ${
                        studentId === s.id ? "bg-primary text-primary-foreground" : "hover:bg-muted"
                      }`}
                    >
                      {studentFullName(s)}{" "}
                      <span className="opacity-70">
                        · {s.grade} {s.section}
                      </span>
                    </button>
                  ))}
                  {matches.length === 0 && (
                    <p className="px-3 py-2 text-sm text-muted-foreground">No learners found.</p>
                  )}
                </div>
              </div>
            ) : (
              <div className="space-y-2">
                <Label>Grade level</Label>
                <Select value={grade} onValueChange={setGrade}>
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
            )}

            <Button className="w-full" onClick={generate} disabled={busy}>
              Generate token{mode === "grade" ? "s" : ""}
            </Button>
            <Button variant="outline" className="w-full" onClick={() => window.print()}>
              <Printer className="mr-2 h-4 w-4" /> Print token list
            </Button>
            <Button variant="destructive" className="w-full" onClick={deleteUnused}>
              <Trash2 className="mr-2 h-4 w-4" /> Delete unused tokens
            </Button>
          </CardContent>
        </Card>

        <Card className="card-shadow">
          <CardHeader>
            <CardTitle className="text-base">
              Tokens{" "}
              <Badge variant="secondary">
                {tokens.filter((t) => !t.used).length} unused / {tokens.length} total
              </Badge>
            </CardTitle>
          </CardHeader>
          <CardContent className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Code</TableHead>
                  <TableHead>Assigned to</TableHead>
                  <TableHead>Grade</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead />
                </TableRow>
              </TableHeader>
              <TableBody>
                {tokens.map((t) => (
                  <TableRow key={t.id}>
                    <TableCell className="font-mono font-semibold tracking-widest">
                      {t.code}
                    </TableCell>
                    <TableCell>
                      {t.student_id ? (nameById.get(t.student_id) ?? "—") : "Unassigned"}
                    </TableCell>
                    <TableCell>{t.grade ?? "—"}</TableCell>
                    <TableCell>
                      <Badge variant={t.used ? "secondary" : "default"}>
                        {t.used ? "Used" : "Unused"}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-right">
                      <Button variant="ghost" size="icon" onClick={() => deleteToken(t.id)}>
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
                {tokens.length === 0 && (
                  <TableRow>
                    <TableCell colSpan={5} className="py-10 text-center text-muted-foreground">
                      No tokens generated yet.
                    </TableCell>
                  </TableRow>
                )}
              </TableBody>
            </Table>
          </CardContent>
        </Card>
      </div>
    </AdminLayout>
  );
}