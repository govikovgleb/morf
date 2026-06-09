"use client";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

export default function UITestPage() {
  return (
    <div className="min-h-screen p-8 space-y-12 bg-bg-primary">
      <h1 className="text-4xl font-cinzel text-text-primary uppercase tracking-wider mb-8">
        UI Components Test
      </h1>

      {/* Buttons Section */}
      <section className="space-y-4">
        <h2 className="text-2xl font-cinzel text-accent uppercase tracking-wider">Buttons</h2>
        <div className="flex flex-wrap gap-4">
          <Button variant="primary">Primary</Button>
          <Button variant="secondary">Secondary</Button>
          <Button variant="ghost">Ghost</Button>
          <Button variant="destructive">Destructive</Button>
          <Button variant="primary" size="sm">Small</Button>
          <Button variant="primary" size="lg">Large</Button>
          <Button variant="primary" disabled>Disabled</Button>
        </div>
      </section>

      {/* Inputs Section */}
      <section className="space-y-4">
        <h2 className="text-2xl font-cinzel text-accent uppercase tracking-wider">Inputs</h2>
        <div className="space-y-4 max-w-md">
          <Input placeholder="Default input" />
          <Input variant="error" placeholder="Error state" />
          <Input disabled placeholder="Disabled input" />
          <Input size="sm" placeholder="Small input" />
          <Input size="lg" placeholder="Large input" />
        </div>
      </section>

      {/* Cards Section */}
      <section className="space-y-4">
        <h2 className="text-2xl font-cinzel text-accent uppercase tracking-wider">Cards</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <Card>
            <CardHeader>
              <CardTitle>Default Card</CardTitle>
              <CardDescription>Standard card with border</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-text-secondary">Card content goes here</p>
            </CardContent>
            <CardFooter>
              <Button variant="primary" size="sm">Action</Button>
            </CardFooter>
          </Card>

          <Card variant="elevated">
            <CardHeader>
              <CardTitle>Elevated Card</CardTitle>
              <CardDescription>Card with shadow</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-text-secondary">Elevated content</p>
            </CardContent>
          </Card>

          <Card variant="ghost">
            <CardHeader>
              <CardTitle>Ghost Card</CardTitle>
              <CardDescription>No border or background</CardDescription>
            </CardHeader>
            <CardContent>
              <p className="text-text-secondary">Ghost content</p>
            </CardContent>
          </Card>
        </div>
      </section>

      {/* Dialog Section */}
      <section className="space-y-4">
        <h2 className="text-2xl font-cinzel text-accent uppercase tracking-wider">Dialog</h2>
        <Dialog>
          <DialogTrigger asChild>
            <Button variant="primary">Open Dialog</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Test Dialog</DialogTitle>
              <DialogDescription>
                This is a dialog with focus trap and escape to close. Gothic themed overlay.
              </DialogDescription>
            </DialogHeader>
            <div className="space-y-4">
              <Input placeholder="Enter something..." />
              <p className="text-text-secondary text-sm">
                Press Escape or click outside to close.
              </p>
            </div>
            <DialogFooter>
              <Button variant="secondary">Cancel</Button>
              <Button variant="primary">Confirm</Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </section>

      {/* Dropdown Menu Section */}
      <section className="space-y-4">
        <h2 className="text-2xl font-cinzel text-accent uppercase tracking-wider">Dropdown Menu</h2>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button variant="secondary">Open Menu</Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent className="w-56">
            <DropdownMenuLabel>Week Selector</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem>Week 1: Portraits</DropdownMenuItem>
            <DropdownMenuItem>Week 2: Urban</DropdownMenuItem>
            <DropdownMenuItem>Week 3: Fantasy</DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem disabled>Future Week</DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </section>

      {/* Color Palette Test */}
      <section className="space-y-4">
        <h2 className="text-2xl font-cinzel text-accent uppercase tracking-wider">Color Palette</h2>
        <div className="flex flex-wrap gap-4">
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-bg-primary border border-border" />
            <span className="text-xs text-text-secondary">bg-primary</span>
          </div>
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-bg-surface" />
            <span className="text-xs text-text-secondary">bg-surface</span>
          </div>
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-bg-surface-elevated" />
            <span className="text-xs text-text-secondary">bg-elevated</span>
          </div>
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-accent" />
            <span className="text-xs text-text-secondary">accent</span>
          </div>
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-text-primary" />
            <span className="text-xs text-text-secondary">text-primary</span>
          </div>
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-text-secondary" />
            <span className="text-xs text-text-secondary">text-secondary</span>
          </div>
          <div className="space-y-2 text-center">
            <div className="w-16 h-16 rounded-sm bg-border" />
            <span className="text-xs text-text-secondary">border</span>
          </div>
        </div>
      </section>
    </div>
  );
}
