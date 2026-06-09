"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useAuthStore } from "@/stores/auth";
import { api } from "@/lib/api/client";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";

const registerSchema = z.object({
  nickname: z
    .string()
    .min(3, "Nickname must be at least 3 characters")
    .max(50, "Nickname must be at most 50 characters")
    .regex(/^[a-zA-Z0-9_-]+$/, "Only letters, numbers, underscores, and hyphens allowed"),
});

const recoverSchema = z.object({
  recoveryCode: z
    .string()
    .min(12, "Recovery code must be at least 12 characters")
    .max(12, "Recovery code must be exactly 12 characters"),
});

type RegisterForm = z.infer<typeof registerSchema>;
type RecoverForm = z.infer<typeof recoverSchema>;

interface AuthModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export function AuthModal({ open, onOpenChange }: AuthModalProps) {
  const [activeTab, setActiveTab] = useState<"register" | "recover">("register");
  const [recoveryCode, setRecoveryCode] = useState<string | null>(null);
  const [isCopied, setIsCopied] = useState(false);
  const [isSaved, setIsSaved] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const setToken = useAuthStore((state) => state.setToken);

  const registerForm = useForm<RegisterForm>({
    resolver: zodResolver(registerSchema),
  });

  const recoverForm = useForm<RecoverForm>({
    resolver: zodResolver(recoverSchema),
  });

  const handleRegister = async (data: RegisterForm) => {
    setIsLoading(true);
    setError(null);
    try {
      const response = await api.register(data.nickname);
      setToken(response.token);
      // For MVP, we don't get recovery code from backend, but we can show a success message
      // In the future, the backend will return recovery_code
      onOpenChange(false);
      registerForm.reset();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Registration failed");
    } finally {
      setIsLoading(false);
    }
  };

  const handleRecover = async (data: RecoverForm) => {
    setIsLoading(true);
    setError(null);
    try {
      const response = await api.recover(data.recoveryCode);
      setToken(response.token);
      onOpenChange(false);
      recoverForm.reset();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Recovery failed");
    } finally {
      setIsLoading(false);
    }
  };

  const copyRecoveryCode = () => {
    if (recoveryCode) {
      navigator.clipboard.writeText(recoveryCode);
      setIsCopied(true);
      setTimeout(() => setIsCopied(false), 2000);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[425px] bg-bg-surface border-border">
        <DialogHeader>
          <DialogTitle className="text-center font-cinzel text-2xl tracking-wider">
            {activeTab === "register" ? "Registration" : "Recovery"}
          </DialogTitle>
        </DialogHeader>

        {/* Tabs */}
        <div className="flex border-b border-border">
          <button
            className={`flex-1 py-3 text-sm font-cinzel uppercase tracking-wider transition-colors ${
              activeTab === "register"
                ? "text-accent border-b-2 border-accent"
                : "text-text-secondary hover:text-text-primary"
            }`}
            onClick={() => {
              setActiveTab("register");
              setError(null);
            }}
          >
            Registration
          </button>
          <button
            className={`flex-1 py-3 text-sm font-cinzel uppercase tracking-wider transition-colors ${
              activeTab === "recover"
                ? "text-accent border-b-2 border-accent"
                : "text-text-secondary hover:text-text-primary"
            }`}
            onClick={() => {
              setActiveTab("recover");
              setError(null);
            }}
          >
            Recovery
          </button>
        </div>

        {/* Error Message */}
        {error && (
          <div className="p-3 bg-red-900/20 border border-red-600 rounded-sm text-red-400 text-sm">
            {error}
          </div>
        )}

        {/* Registration Form */}
        {activeTab === "register" && (
          <form onSubmit={registerForm.handleSubmit(handleRegister)} className="space-y-4">
            <div>
              <label className="text-sm font-cinzel text-text-secondary uppercase tracking-wider">
                Nickname
              </label>
              <Input
                placeholder="Enter your nickname"
                {...registerForm.register("nickname")}
                className="mt-1"
              />
              {registerForm.formState.errors.nickname && (
                <p className="text-red-400 text-xs mt-1">
                  {registerForm.formState.errors.nickname.message}
                </p>
              )}
            </div>
            <Button
              type="submit"
              variant="primary"
              className="w-full"
              disabled={isLoading}
            >
              {isLoading ? "Registering..." : "Register"}
            </Button>
          </form>
        )}

        {/* Recovery Form */}
        {activeTab === "recover" && (
          <form onSubmit={recoverForm.handleSubmit(handleRecover)} className="space-y-4">
            <div>
              <label className="text-sm font-cinzel text-text-secondary uppercase tracking-wider">
                Recovery Code
              </label>
              <Input
                placeholder="Enter your recovery code"
                {...recoverForm.register("recoveryCode")}
                className="mt-1"
              />
              {recoverForm.formState.errors.recoveryCode && (
                <p className="text-red-400 text-xs mt-1">
                  {recoverForm.formState.errors.recoveryCode.message}
                </p>
              )}
            </div>
            <Button
              type="submit"
              variant="primary"
              className="w-full"
              disabled={isLoading}
            >
              {isLoading ? "Recovering..." : "Recover Account"}
            </Button>
          </form>
        )}
      </DialogContent>

      {/* Recovery Code Modal */}
      <Dialog open={!!recoveryCode} onOpenChange={() => setRecoveryCode(null)}>
        <DialogContent className="sm:max-w-[500px] bg-bg-surface border-border">
          <DialogHeader>
            <DialogTitle className="text-center font-cinzel text-xl tracking-wider text-accent">
              SAVE RECOVERY CODE
            </DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <p className="text-text-secondary text-center">
              This code is required to recover your account. Save it in a secure place.
            </p>
            <div className="p-4 bg-bg-primary border border-accent rounded-sm">
              <code className="text-2xl font-mono text-accent tracking-wider block text-center">
                {recoveryCode}
              </code>
            </div>
            <div className="flex gap-2">
              <Button
                variant="secondary"
                className="flex-1"
                onClick={copyRecoveryCode}
              >
                {isCopied ? "Copied!" : "Copy"}
              </Button>
            </div>
            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="saved"
                checked={isSaved}
                onChange={(e) => setIsSaved(e.target.checked)}
                className="w-4 h-4 accent-accent"
              />
              <label htmlFor="saved" className="text-sm text-text-secondary">
                I saved the code
              </label>
            </div>
            <Button
              variant="primary"
              className="w-full"
              disabled={!isSaved}
              onClick={() => setRecoveryCode(null)}
            >
              Continue
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </Dialog>
  );
}
