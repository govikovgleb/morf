import * as React from "react";
import { cva, type VariantProps } from "class-variance-authority";
import { cn } from "@/lib/utils";

const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-sm text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 uppercase tracking-wider font-cinzel",
  {
    variants: {
      variant: {
        primary:
          "border border-accent text-accent hover:bg-accent hover:text-white hover:glow-accent active:bg-accent-hover",
        secondary:
          "border border-border text-text-secondary hover:border-accent hover:text-accent",
        ghost:
          "border-transparent text-text-secondary hover:text-accent hover:bg-bg-surface",
        destructive:
          "border border-red-600 text-red-600 hover:bg-red-600 hover:text-white",
      },
      size: {
        sm: "h-9 px-4 py-2 text-xs",
        md: "h-11 px-6 py-3 text-sm",
        lg: "h-14 px-8 py-4 text-base",
        icon: "h-10 w-10 p-2",
      },
    },
    defaultVariants: {
      variant: "primary",
      size: "md",
    },
  }
);

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement>,
    VariantProps<typeof buttonVariants> {
  asChild?: boolean;
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant, size, asChild = false, ...props }, ref) => {
    return (
      <button
        className={cn(buttonVariants({ variant, size, className }))}
        ref={ref}
        {...props}
      />
    );
  }
);
Button.displayName = "Button";

export { Button, buttonVariants };
