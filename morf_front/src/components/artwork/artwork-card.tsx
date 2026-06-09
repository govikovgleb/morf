"use client";

import { useState } from "react";
import { useAuthStore } from "@/stores/auth";
import { useLikeStore } from "@/stores/likes";
import { useToggleLike } from "@/hooks/feed";
import type { Artwork } from "@/types/api";

interface ArtworkCardProps {
  artwork: Artwork;
  onClick: () => void;
}

export function ArtworkCard({ artwork, onClick }: ArtworkCardProps) {
  const [isLoading, setIsLoading] = useState(false);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const { isLiked, toggleLike: toggleLikeStore } = useLikeStore();
  const { toggleLike } = useToggleLike();

  const liked = isLiked(artwork.id);
  const displayCount = artwork.likes_count;

  const handleLike = async (e: React.MouseEvent) => {
    e.stopPropagation();
    
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
    <div
      className="bg-bg-surface gothic-border rounded-sm overflow-hidden cursor-pointer group"
      onClick={onClick}
    >
      <div className="relative aspect-[3/4]">
        <img
          src={artwork.cdn_url}
          alt={artwork.caption || `Artwork by ${artwork.author_nickname}`}
          className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
        />
      </div>
      <div className="p-3 border-t border-border flex items-center justify-between">
        <span className="text-sm font-cinzel text-text-secondary uppercase tracking-wider">
          {artwork.author_nickname}
        </span>
        <button
          onClick={handleLike}
          disabled={!isAuthenticated || isLoading}
          className={`flex items-center gap-1 text-sm transition-colors ${
            liked
              ? "text-accent"
              : "text-text-secondary hover:text-accent"
          } ${!isAuthenticated ? "opacity-50 cursor-not-allowed" : ""}`}
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill={liked ? "currentColor" : "none"}
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
          >
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
          </svg>
          {displayCount}
        </button>
      </div>
    </div>
  );
}
