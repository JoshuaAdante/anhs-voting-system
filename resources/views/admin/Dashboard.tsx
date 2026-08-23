import { useQuery } from "@tanstack/react-query";
import { useEffect } from "react";
import { Users, CheckCircle2, PieChart, Award, Ticket } from "lucide-react";

import { AdminLayout } from "@/components/AdminLayout";
import { supabase } from "@/integrations/supabase/client";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";

type Stats = {
  students: number;
  voted: number;
  candidates: number;
  tokens: number;
  usedTokens: number;
  results: { position: string; sort: number; rows: { name: string; votes: number }[] }[];
};

async function loadStats(): Promise<Stats> {
  const [students, voted, candidatesRes, tokens, usedTokens, positionsRes, votesRes] =
    await Promise.all([
      supabase.from("students").select("id", { count: "exact", head: true }),
      supabase
        .from("students")
        .select("id", { count: "exact", head: true })
        .eq("has_voted", true),
      supabase.from("candidates").select("id, full_name, position_id"),
      supabase.from("voting_tokens").select("id", { count: "exact", head: true }),
      supabase.from("voting_tokens").select("id", { count: "exact", head: true }).eq("used", true),
      supabase.from("positions").select("id, name, sort_order").order("sort_order"),
      supabase.from("votes").select("candidate_id"),
    ]);

  const tally = new Map<string, number>();
  for (const v of votesRes.data ?? [])
    tally.set(v.candidate_id, (tally.get(v.candidate_id) ?? 0) + 1);

  const results = (positionsRes.data ?? []).map((p) => ({
    position: p.name,
    sort: p.sort_order,
    rows: (candidatesRes.data ?? [])
      .filter((c) => c.position_id === p.id)
      .map((c) => ({ name: c.full_name, votes: tally.get(c.id) ?? 0 }))
      .sort((a, b) => b.votes - a.votes),
  }));

  return {
    students: students.count ?? 0,
    voted: voted.count ?? 0,
    candidates: candidatesRes.data?.length ?? 0,
    tokens: tokens.count ?? 0,
    usedTokens: usedTokens.count ?? 0,
    results,
  };
}

export function DashboardPage() {
  const { data, refetch } = useQuery({ queryKey: ["dashboard"], queryFn: loadStats });

  useEffect(() => {
    const channel = supabase
      .channel("dashboard-live")
      .on("postgres_changes", { event: "*", schema: "public", table: "votes" }, () => refetch())
      .subscribe();
    const timer = setInterval(() => refetch(), 15000);
    return () => {
      supabase.removeChannel(channel);
      clearInterval(timer);
    };
  }, [refetch]);

  const turnout = data && data.students > 0 ? Math.round((data.voted / data.students) * 100) : 0;

  const stats = [
    { label: "Total Students", value: data?.students ?? 0, icon: Users },
    { label: "Already Voted", value: data?.voted ?? 0, icon: CheckCircle2 },
    { label: "Turnout", value: `${turnout}%`, icon: PieChart },
    { label: "Candidates", value: data?.candidates ?? 0, icon: Award },
    { label: "Tokens Used", value: `${data?.usedTokens ?? 0} / ${data?.tokens ?? 0}`, icon: Ticket },
  ];

  return (
    <AdminLayout title="Dashboard" subtitle="Quick stats and live election results">
      <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        {stats.map((s) => (
          <Card key={s.label} className="card-shadow">
            <CardContent className="flex items-center gap-4 p-5">
              <span className="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl brand-gradient text-primary-foreground">
                <s.icon className="h-5 w-5" />
              </span>
              <div className="min-w-0">
                <p className="text-xs font-medium uppercase tracking-wide text-muted-foreground">
                  {s.label}
                </p>
                <p className="truncate text-2xl font-bold">{s.value}</p>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      <Card className="mt-6 card-shadow">
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            Voter turnout
            <Badge variant="secondary">
              {data?.voted ?? 0} of {data?.students ?? 0} learners
            </Badge>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <Progress value={turnout} />
          <p className="mt-2 text-sm text-muted-foreground">{turnout}% turnout</p>
        </CardContent>
      </Card>

      <h2 className="mt-8 text-lg font-bold">Live Election Results</h2>
      <div className="mt-4 grid gap-5 lg:grid-cols-2">
        {(data?.results ?? []).map((r) => {
          const total = r.rows.reduce((a, b) => a + b.votes, 0);
          return (
            <Card key={r.position} className="card-shadow">
              <CardHeader className="pb-3">
                <CardTitle className="text-base">{r.position}</CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                {r.rows.length === 0 && (
                  <p className="text-sm text-muted-foreground">No candidates yet.</p>
                )}
                {r.rows.map((c, i) => (
                  <div key={c.name}>
                    <div className="flex items-center justify-between text-sm">
                      <span className="font-medium">
                        {i === 0 && total > 0 ? "🏆 " : ""}
                        {c.name}
                      </span>
                      <span className="text-muted-foreground">
                        {c.votes} ({total ? Math.round((c.votes / total) * 100) : 0}%)
                      </span>
                    </div>
                    <Progress className="mt-1.5" value={total ? (c.votes / total) * 100 : 0} />
                  </div>
                ))}
              </CardContent>
            </Card>
          );
        })}
      </div>
    </AdminLayout>
  );
}