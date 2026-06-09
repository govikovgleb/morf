import type { Metadata } from "next";
import { Cinzel, Inter } from "next/font/google";
import "./globals.css";
import { Providers } from "@/components/providers/providers";
import { Header } from "@/components/layout/header";
import { AuthInitializer } from "@/components/auth/auth-initializer";
import { ErrorBoundary } from "@/components/error-boundary";
import { AuthGuard } from "@/components/auth/auth-guard";

const cinzel = Cinzel({
  variable: "--font-cinzel",
  subsets: ["latin"],
  display: "swap",
});

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
  display: "swap",
});

export const metadata: Metadata = {
  title: "MORF — Weekly Character Design Challenges",
  description: "Anonymous artist community for weekly character-design challenges",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="en"
      className={`${cinzel.variable} ${inter.variable} h-full antialiased`}
    >
      <body className="min-h-full flex flex-col bg-bg-primary text-text-primary">
        <Providers>
          <AuthInitializer />
          <Header />
          <main className="flex-1 relative">
            <ErrorBoundary>
              <AuthGuard>
                {children}
              </AuthGuard>
            </ErrorBoundary>
          </main>
        </Providers>
      </body>
    </html>
  );
}
