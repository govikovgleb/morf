import { create } from "zustand";
import { persist } from "zustand/middleware";

interface AuthState {
  token: string | null;
  isAuthenticated: boolean;
  setToken: (token: string) => void;
  logout: () => void;
  initialize: () => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      token: null,
      isAuthenticated: false,
      setToken: (token) => {
        set({ token, isAuthenticated: true });
        if (typeof window !== "undefined") {
          localStorage.setItem("auth_token", token);
        }
      },
      logout: () => {
        set({ token: null, isAuthenticated: false });
        if (typeof window !== "undefined") {
          localStorage.removeItem("auth_token");
        }
      },
      initialize: () => {
        if (typeof window !== "undefined") {
          const token = localStorage.getItem("auth_token");
          if (token) {
            set({ token, isAuthenticated: true });
          }
        }
      },
    }),
    {
      name: "auth-storage",
      partialize: (state) => ({ token: state.token, isAuthenticated: state.isAuthenticated }),
    }
  )
);
