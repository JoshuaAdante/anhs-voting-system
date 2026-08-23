import { useQuery } from "@tanstack/react-query";
import { useState } from "react";
import { toast } from "sonner";
import { Plus, Pencil, Trash2 } from "lucide-react";

import { AdminLayout } from "@/components/AdminLayout";
import { supabase } from "@/integrations/supabase/client";
import { GRADES } from "@/lib/brand";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

type CandidateForm = {
  id?: string;
  full_name: string;
  position_id: string;
  partylist: string;
  grade: string;
  section: string;
  photo_url: string;
};

const blank: CandidateForm = {
  full_name: "",
  position_id: "",
  partylist: "",
  grade: GRADES[0]!,
  section: "",
  photo_url: "",
};

export function CandidatesPage() {
  const [form, setForm] = useState<CandidateForm>(blank);
  const [open, setOpen] = useState(false);
  const [positionName, setPositionName] = useState("");

  const positionsQ = useQuery({
    queryKey: ["positions"],
    queryFn: async () => {
      const { data, error } = await supabase
        .from("positions")
        .select("*")
        .order("sort_order");
      if (error) throw error;
      return data;
    },
  });

  const candidatesQ = useQuery({
    queryKey: ["candidates"],
    queryFn: async () => {
      const { data, error } = await supabase.from("candidates").select("*").order("full_name");
      if (error) throw error;
      return data;
    },
  });

  const positions = positionsQ.data ?? [];
  const candidates = candidatesQ.data ?? [];

  function openNew() {
    setForm({ ...blank, position_id: positions[0]?.id ?? "" });
    setOpen(true);
  }

  async function saveCandidate() {
    if (!form.full_name || !form.position_id) {
      toast.error("Name and position are required.");
      return;
    }
    const payload = {
      full_name: form.full_name,
      position_id: form.position_id,
      partylist: form.partylist || null,
      grade: form.grade || null,
      section: form.section || null,
      photo_url: form.photo_url || null,
    };
    const { error } = form.id
      ? await supabase.from("candidates").update(payload).eq("id", form.id)
      : await supabase.from("candidates").insert(payload);
    if (error) {
      toast.error(error.message);
      return;
    }
    toast.success(form.id ? "Candidate updated." : "Candidate added.");
    setOpen(false);
    candidatesQ.refetch();
  }

  async function deleteCandidate(id: string) {
    if (!confirm("Delete this candidate?")) return;
    const { error } = await supabase.from("candidates").delete().eq("id", id);
    if (error) {
      toast.error(error.message);
      return;
    }
    candidatesQ.refetch();
  }

  async function addPosition() {
    if (!positionName.trim()) return;
    const { error } = await supabase.from("positions").insert({
      name: positionName.trim(),
      sort_order: positions.length + 1,
    });
    if (error) {
      toast.error(error.message);
      return;
    }
    setPositionName("");
    positionsQ.refetch();
  }

  async function deletePosition(id: string) {
    if (!confirm("Delete this position and all its candidates?")) return;
    const { error } = await supabase.from("positions").delete().eq("id", id);
    if (error) {
      toast.error(error.message);
      return;
    }
    positionsQ.refetch();
    candidatesQ.refetch();
  }

  return (
    <AdminLayout title="Candidate Management" subtitle="Candidates and elective positions">
      <Tabs defaultValue="candidates">
        <TabsList>
          <TabsTrigger value="candidates">All Candidates</TabsTrigger>
          <TabsTrigger value="positions">Manage Positions</TabsTrigger>
        </TabsList>

        <TabsContent value="candidates" className="mt-5">
          <Button onClick={openNew}>
            <Plus className="mr-2 h-4 w-4" /> Add Candidate
          </Button>

          <div className="mt-5 space-y-6">
            {positions.map((p) => {
              const list = candidates.filter((c) => c.position_id === p.id);
              return (
                <Card key={p.id} className="card-shadow">
                  <CardHeader className="pb-3">
                    <CardTitle className="flex items-center gap-2 text-base">
                      {p.name}
                      <Badge variant="secondary">{list.length}</Badge>
                    </CardTitle>
                  </CardHeader>
                  <CardContent className="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    {list.length === 0 && (
                      <p className="text-sm text-muted-foreground">No candidates yet.</p>
                    )}
                    {list.map((c) => (
                      <div
                        key={c.id}
                        className="flex items-center gap-3 rounded-xl border border-border p-3"
                      >
                        {c.photo_url ? (
                          <img
                            src={c.photo_url}
                            alt={c.full_name}
                            className="h-12 w-12 rounded-full object-cover"
                          />
                        ) : (
                          <span className="flex h-12 w-12 items-center justify-center rounded-full brand-gradient text-sm font-bold text-primary-foreground">
                            {c.full_name.charAt(0)}
                          </span>
                        )}
                        <div className="min-w-0 flex-1">
                          <p className="truncate font-medium">{c.full_name}</p>
                          <p className="truncate text-xs text-muted-foreground">
                            {[c.partylist, c.grade, c.section].filter(Boolean).join(" · ") || "—"}
                          </p>
                        </div>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => {
                            setForm({
                              id: c.id,
                              full_name: c.full_name,
                              position_id: c.position_id,
                              partylist: c.partylist ?? "",
                              grade: c.grade ?? GRADES[0]!,
                              section: c.section ?? "",
                              photo_url: c.photo_url ?? "",
                            });
                            setOpen(true);
                          }}
                        >
                          <Pencil className="h-4 w-4" />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => deleteCandidate(c.id)}
                        >
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    ))}
                  </CardContent>
                </Card>
              );
            })}
          </div>
        </TabsContent>

        <TabsContent value="positions" className="mt-5">
          <Card className="card-shadow">
            <CardContent className="p-5">
              <div className="flex gap-2">
                <Input
                  placeholder="New position name"
                  value={positionName}
                  onChange={(e) => setPositionName(e.target.value)}
                />
                <Button onClick={addPosition}>Add</Button>
              </div>
              <div className="mt-5 space-y-2">
                {positions.map((p) => (
                  <div
                    key={p.id}
                    className="flex items-center justify-between rounded-lg border border-border px-4 py-3"
                  >
                    <span className="font-medium">{p.name}</span>
                    <Button variant="ghost" size="icon" onClick={() => deletePosition(p.id)}>
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <Dialog open={open} onOpenChange={setOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{form.id ? "Edit candidate" : "Add candidate"}</DialogTitle>
          </DialogHeader>
          <div className="grid gap-4 sm:grid-cols-2">
            <div className="space-y-2 sm:col-span-2">
              <Label>Full name</Label>
              <Input
                value={form.full_name}
                onChange={(e) => setForm({ ...form, full_name: e.target.value })}
              />
            </div>
            <div className="space-y-2 sm:col-span-2">
              <Label>Position</Label>
              <Select
                value={form.position_id}
                onValueChange={(v) => setForm({ ...form, position_id: v })}
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select position" />
                </SelectTrigger>
                <SelectContent>
                  {positions.map((p) => (
                    <SelectItem key={p.id} value={p.id}>
                      {p.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div className="space-y-2">
              <Label>Partylist</Label>
              <Input
                value={form.partylist}
                onChange={(e) => setForm({ ...form, partylist: e.target.value })}
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
              <Label>Photo URL (optional)</Label>
              <Input
                value={form.photo_url}
                onChange={(e) => setForm({ ...form, photo_url: e.target.value })}
              />
            </div>
          </div>
          <DialogFooter>
            <Button onClick={saveCandidate}>Save candidate</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AdminLayout>
  );
}