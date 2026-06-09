"use client";

import { useState } from "react";
import Link from "next/link";
import { useAuthStore } from "@/stores/auth";
import { AuthModal } from "@/components/auth/auth-modal";

export function Header() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
  const { isAuthenticated, logout } = useAuthStore();

  return (
    <>
      <header className="sticky top-0 z-50 w-full border-b border-border bg-bg-primary/80 backdrop-blur-md">
        <div className="container mx-auto px-4 h-16 flex items-center justify-between">
          {/* Logo */}
          <Link 
            href="/" 
            className="font-cinzel text-2xl tracking-[0.3em] text-text-primary uppercase hover:text-accent transition-colors duration-300"
          >
            MORF
          </Link>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center gap-6">
            <Link
              href="/feed"
              className="text-sm font-cinzel uppercase tracking-wider text-text-secondary hover:text-accent transition-colors duration-300 min-h-[44px] min-w-[44px] flex items-center"
            >
              Feed
            </Link>
            {isAuthenticated ? (
              <button
                onClick={logout}
                className="text-sm font-cinzel uppercase tracking-wider text-text-secondary border border-border px-4 py-2 hover:border-accent hover:text-accent transition-all duration-300 min-h-[44px] min-w-[44px]"
              >
                Logout
              </button>
            ) : (
              <button
                onClick={() => setIsAuthModalOpen(true)}
                className="text-sm font-cinzel uppercase tracking-wider text-accent border border-accent px-4 py-2 hover:bg-accent hover:text-white transition-all duration-300 min-h-[44px] min-w-[44px]"
              >
                Sign In
              </button>
            )}
          </nav>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden p-2 text-text-secondary hover:text-accent transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            aria-label="Toggle menu"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              strokeWidth="2"
              strokeLinecap="round"
              strokeLinejoin="round"
            >
              {isMenuOpen ? (
                <>
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </>
              ) : (
                <>
                  <line x1="4" y1="12" x2="20" y2="12" />
                  <line x1="4" y1="6" x2="20" y2="6" />
                  <line x1="4" y1="18" x2="20" y2="18" />
                </>
              )}
            </svg>
          </button>
        </div>

        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="md:hidden border-t border-border bg-bg-primary">
            <nav className="container mx-auto px-4 py-4 flex flex-col gap-4">
              <Link
                href="/feed"
                className="text-sm font-cinzel uppercase tracking-wider text-text-secondary hover:text-accent transition-colors duration-300 min-h-[44px] flex items-center"
                onClick={() => setIsMenuOpen(false)}
              >
                Feed
              </Link>
              {isAuthenticated ? (
                <button
                  onClick={() => {
                    logout();
                    setIsMenuOpen(false);
                  }}
                  className="text-sm font-cinzel uppercase tracking-wider text-text-secondary border border-border px-4 py-2 hover:border-accent hover:text-accent transition-all duration-300 min-h-[44px]"
                >
                  Logout
                </button>
              ) : (
                <button
                  onClick={() => {
                    setIsAuthModalOpen(true);
                    setIsMenuOpen(false);
                  }}
                  className="text-sm font-cinzel uppercase tracking-wider text-accent border border-accent px-4 py-2 hover:bg-accent hover:text-white transition-all duration-300 min-h-[44px]"
                >
                  Sign In
                </button>
              )}
            </nav>
          </div>
        )}
      </header>

      <AuthModal open={isAuthModalOpen} onOpenChange={setIsAuthModalOpen} />
    </>
  );
}
