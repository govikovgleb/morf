"use client";

import { useState } from "react";
import { useAuthStore } from "@/stores/auth";
import { useLikeStore } from "@/stores/likes";
import { useToggleLike } from "@/hooks/feed";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import type { Artwork } from "@/types/api";

interface ArtworkModalProps {
  artwork: Artwork | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export function ArtworkModal({ artwork, open, onOpenChange }: ArtworkModalProps) {
  const [isLoading, setIsLoading] = useState(false);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const { isLiked, toggleLike: toggleLikeStore } = useLikeStore();
  const { toggleLike } = useToggleLike();

  if (!artwork) return null;

  const liked = isLiked(artwork.id);
  const displayCount = artwork.likes_count;

  const handleLike = async () => {
    if (!isAuthenticated || isLoading) return;

    setIsLoading(true);

    try {
      await toggleLike(artwork.id);
      toggleLikeStore(artwork.id);
      // Note: We do NOT invalidate queries here to avoid:
      // 1. Re-sorting the feed
      // 2. Double-counting likes (server count + local count)
      // The like state is kept in the global store for the session
    } catch (error) {
      console.error("Like failed", error);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[800px] bg-bg-surface border-border p-0">
        <DialogHeader className="sr-only">
          <DialogTitle>Artwork Detail</DialogTitle>
        </DialogHeader>
        
        <div className="flex flex-col md:flex-row">
          {/* Image */}
          <div className="relative md:w-2/3 aspect-square md:aspect-auto">
            <img
              src={artwork.cdn_url}
              alt={artwork.caption || `Artwork by ${artwork.author_nickname}`}
              className="w-full h-full object-contain bg-bg-primary"
            />
          </div>
          
          {/* Info */}
          <div className="p-6 md:w-1/3 flex flex-col justify-between">
            <div className="space-y-4">
              <div>
                <p className="text-sm font-cinzel text-text-secondary uppercase tracking-wider mb-1">
                  Author
                </p>
                <p className="text-lg font-cinzel text-text-primary uppercase tracking-wider">
                  {artwork.author_nickname}
                </p>
              </div>
              
              {artwork.caption && (
                <div>
                  <p className="text-sm font-cinzel text-text-secondary uppercase tracking-wider mb-1">
                    Caption
                  </p>
                  <p className="text-text-primary font-inter">
                    {artwork.caption}
                  </p>
                </div>
              )}
              
              <div>
                <p className="text-sm font-cinzel text-text-secondary uppercase tracking-wider mb-1">
                  Status
                </p>
                <p className="text-text-primary font-inter capitalize">
                  {artwork.status}
                </p>
              </div>
            </div>
            
            {/* Like button */}
            <div className="pt-6 border-t border-border mt-4">
              <button
                onClick={handleLike}
                disabled={!isAuthenticated || isLoading}
                className={`flex items-center gap-2 w-full justify-center py-3 border rounded-sm transition-all ${
                  liked
                    ? "border-accent text-accent bg-accent/10"
                    : "border-border text-text-secondary hover:border-accent hover:text-accent"
                } ${!isAuthenticated ? "opacity-50 cursor-not-allowed" : ""}`}
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill={liked ? "currentColor" : "none"}
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
                <span className="font-cinzel uppercase tracking-wider">
                  {liked ? "Liked" : "Like"} ({displayCount})
                </span>
              </button>
            </div>
          </div>
        </div>
      </DialogContent>
    </Dialog>
  );
}
