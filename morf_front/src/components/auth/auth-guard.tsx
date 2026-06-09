"use client";

import { useEffect } from "react";
import { useRouter, usePathname } from "next/navigation";
import { useAuthStore } from "@/stores/auth";

interface AuthGuardProps {
  children: React.ReactNode;
  fallback?: React.ReactNode;
}

export function AuthGuard({ children, fallback }: AuthGuardProps) {
  const router = useRouter();
  const pathname = usePathname();
  const { isAuthenticated, initialize } = useAuthStore();

  useEffect(() => {
    initialize();
  }, [initialize]);

  // Public routes that don't require auth
  const publicRoutes = ["/", "/feed", "/ui-test"];
  const isPublicRoute = publicRoutes.includes(pathname);

  if (!isAuthenticated && !isPublicRoute) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[calc(100vh-4rem)] px-4">
        <div className="text-center space-y-4">
          <h2 className="text-2xl font-cinzel text-text-primary uppercase tracking-wider">
            Authentication Required
          </h2>
          <p className="text-text-secondary font-inter">
            Please sign in to access this page
          </p>
          <button
            onClick={() => router.push("/")}
            className="px-6 py-2 border border-accent text-accent uppercase tracking-wider font-cinzel hover:bg-accent hover:text-white transition-all"
          >
            Go Home
          </button>
        </div>
      </div>
    );
  }

  return <>{children}</>;
}
