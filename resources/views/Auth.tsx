import { Link, useNavigate } from "@tanstack/react-router";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import { ArrowLeft, Loader2, Lock } from "lucide-react";

import { supabase } from "@/integrations/supabase/client";
import { ANHS_LOGO, DEPED_LOGO, SCHOOL_NAME, SYSTEM_NAME } from "@/lib/brand";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export function AuthPage() {
  const navigate = useNavigate();
  const [mode, setMode] = useState<"signin" | "signup">("signin");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    supabase.auth.getSession().then(({ data }) => {
      if (data.session) navigate({ to: "/dashboard", replace: true });
    });
  }, [navigate]);

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      if (mode === "signin") {
        const { error } = await supabase.auth.signInWithPassword({ email, password });
        if (error) throw error;
        toast.success("Welcome back, admin.");
        navigate({ to: "/dashboard", replace: true });
      } else {
        const { data, error } = await supabase.auth.signUp({
          email,
          password,
          options: { emailRedirectTo: window.location.origin + "/auth" },
        });
        if (error) throw error;
        if (data.session) {
          toast.success("Admin account created.");
          navigate({ to: "/dashboard", replace: true });
        } else {
          toast.success("Account created. Check your email to confirm, then sign in.");
          setMode("signin");
        }
      }
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Something went wrong.");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="flex min-h-screen">
      <div className="relative hidden w-1/2 flex-col justify-between overflow-hidden brand-gradient p-12 text-primary-foreground lg:flex">
        <div className="absolute inset-0 opacity-15 [background:radial-gradient(circle_at_30%_25%,white,transparent_45%)]" />
        <div className="relative flex items-center gap-4">
          <img src={ANHS_LOGO} alt="Agusan National High School logo" className="h-14 w-14" />
          <img src={DEPED_LOGO} alt="DepEd seal" className="h-14 w-14" />
        </div>
        <div className="relative">
          <p className="text-xs font-semibold uppercase tracking-[0.35em] text-accent">
            Election Committee
          </p>
          <h2 className="mt-4 text-4xl font-extrabold leading-tight">{SYSTEM_NAME}</h2>
          <p className="mt-4 max-w-md text-primary-foreground/75">
            Manage learners, candidates, one-time tokens and live results in one secure console.
          </p>
        </div>
        <p className="relative text-xs text-primary-foreground/60">
          © {new Date().getFullYear()} {SCHOOL_NAME} · Version 2.0
        </p>
      </div>

      <div className="flex w-full items-center justify-center bg-background px-6 py-16 lg:w-1/2">
        <div className="w-full max-w-sm">
          <div className="flex items-center gap-3 lg:hidden">
            <img src={ANHS_LOGO} alt="Agusan National High School logo" className="h-12 w-12" />
            <img src={DEPED_LOGO} alt="DepEd seal" className="h-12 w-12" />
          </div>

          <span className="mt-6 inline-flex h-11 w-11 items-center justify-center rounded-xl brand-gradient text-primary-foreground">
            <Lock className="h-5 w-5" />
          </span>
          <h1 className="mt-5 text-2xl font-bold">
            {mode === "signin" ? "Admin Login" : "Create admin account"}
          </h1>
          <p className="mt-1 text-sm text-muted-foreground">
            {mode === "signin"
              ? "Sign in to the election console."
              : "Register an election committee account."}
          </p>

          <form onSubmit={handleSubmit} className="mt-8 space-y-4">
            <div className="space-y-2">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                type="email"
                autoComplete="email"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="admin@anhs.edu.ph"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password">Password</Label>
              <Input
                id="password"
                type="password"
                autoComplete={mode === "signin" ? "current-password" : "new-password"}
                required
                minLength={6}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
              />
            </div>
            <Button type="submit" className="w-full" disabled={loading}>
              {loading && <Loader2 className="mr-2 h-4 w-4 animate-spin" />}
              {mode === "signin" ? "Login" : "Create account"}
            </Button>
          </form>

          <button
            type="button"
            onClick={() => setMode(mode === "signin" ? "signup" : "signin")}
            className="mt-4 w-full text-sm text-muted-foreground underline-offset-4 hover:underline"
          >
            {mode === "signin"
              ? "First time? Create the admin account"
              : "Already have an account? Sign in"}
          </button>

          <Link
            to="/"
            className="mt-8 inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
          >
            <ArrowLeft className="h-4 w-4" /> Back to Home Page
          </Link>
        </div>
      </div>
    </div>
  );
}