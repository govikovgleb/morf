"use client";

import { useEffect, useState } from "react";

interface ErrorBoundaryProps {
  children: React.ReactNode;
}

interface ErrorBoundaryState {
  hasError: boolean;
  error: Error | null;
}

export function ErrorBoundary({ children }: ErrorBoundaryProps) {
  const [state, setState] = useState<ErrorBoundaryState>({
    hasError: false,
    error: null,
  });

  useEffect(() => {
    const handleError = (error: ErrorEvent) => {
      setState({ hasError: true, error: error.error });
    };

    window.addEventListener("error", handleError);
    return () => window.removeEventListener("error", handleError);
  }, []);

  if (state.hasError) {
    return (
      <div className="flex flex-col items-center justify-center min-h-[calc(100vh-4rem)] px-4">
        <div className="text-center space-y-4 max-w-md">
          <h2 className="text-2xl font-cinzel text-red-600 uppercase tracking-wider">
            Error
          </h2>
          <p className="text-text-secondary font-inter">
            Something went wrong. Please try again later.
          </p>
          {state.error && (
            <pre className="text-xs text-text-secondary/60 bg-bg-surface p-4 rounded-sm overflow-auto max-h-32">
              {state.error.message}
            </pre>
          )}
          <button
            onClick={() => {
              setState({ hasError: false, error: null });
              window.location.reload();
            }}
            className="px-6 py-2 border border-accent text-accent uppercase tracking-wider font-cinzel hover:bg-accent hover:text-white transition-all"
          >
            Reload Page
          </button>
        </div>
      </div>
    );
  }

  return <>{children}</>;
}
