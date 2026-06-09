export function ArtworkSkeleton() {
  return (
    <div className="bg-bg-surface gothic-border rounded-sm overflow-hidden animate-pulse">
      <div className="aspect-[3/4] bg-bg-surface-elevated" />
      <div className="p-3 border-t border-border flex items-center justify-between">
        <div className="h-4 w-24 bg-bg-surface-elevated rounded" />
        <div className="h-4 w-12 bg-bg-surface-elevated rounded" />
      </div>
    </div>
  );
}

export function ReferenceSkeleton() {
  return (
    <div className="bg-bg-surface gothic-border rounded-sm overflow-hidden animate-pulse">
      <div className="aspect-[4/3] bg-bg-surface-elevated" />
      <div className="p-3 border-t border-border bg-bg-surface-elevated">
        <div className="h-4 w-20 mx-auto bg-bg-primary rounded" />
      </div>
    </div>
  );
}

export function SkeletonGrid({ count = 6, type = "artwork" }: { count?: number; type?: "artwork" | "reference" }) {
  const Skeleton = type === "artwork" ? ArtworkSkeleton : ReferenceSkeleton;
  
  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      {Array.from({ length: count }).map((_, i) => (
        <Skeleton key={i} />
      ))}
    </div>
  );
}
