"use client";

import { useState, useEffect, useCallback } from "react";
import { useInfiniteArtworks } from "@/hooks/feed";
import { useReferenceSets } from "@/hooks/api";
import { ArtworkCard } from "@/components/artwork/artwork-card";
import { ArtworkModal } from "@/components/artwork/artwork-modal";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import type { Artwork } from "@/types/api";

export default function FeedPage() {
  const [selectedSetId, setSelectedSetId] = useState<string | null>(null);
  const [selectedArtwork, setSelectedArtwork] = useState<Artwork | null>(null);
  const [isModalOpen, setIsModalOpen] = useState(false);

  const {
    data,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading,
  } = useInfiniteArtworks({ referenceSetId: selectedSetId || undefined });

  const { data: setsData } = useReferenceSets();
  const referenceSets = setsData?.data || [];

  // Flatten pages
  const artworks = data?.pages.flatMap((page) => page.data) || [];

  // Intersection Observer for infinite scroll
  const loadMoreRef = useCallback(
    (node: HTMLDivElement | null) => {
      if (!node) return;
      
      const observer = new IntersectionObserver(
        (entries) => {
          if (entries[0].isIntersecting && hasNextPage && !isFetchingNextPage) {
            fetchNextPage();
          }
        },
        { threshold: 0.1 }
      );

      observer.observe(node);

      return () => observer.disconnect();
    },
    [hasNextPage, isFetchingNextPage, fetchNextPage]
  );

  const handleArtworkClick = (artwork: Artwork) => {
    setSelectedArtwork(artwork);
    setIsModalOpen(true);
  };

  return (
    <div className="min-h-[calc(100vh-4rem)] px-4 py-8">
      <div className="max-w-7xl mx-auto space-y-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
          <h1 className="text-3xl font-cinzel text-text-primary uppercase tracking-wider">
            Feed
          </h1>

          {/* Week Filter */}
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="secondary" className="min-w-[200px]">
                {selectedSetId
                  ? referenceSets.find((s) => s.id === selectedSetId)?.title || "All Weeks"
                  : "All Weeks"}
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="2"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="ml-2"
                >
                  <polyline points="6 9 12 15 18 9" />
                </svg>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent className="w-[200px]">
              <DropdownMenuItem onClick={() => setSelectedSetId(null)}>
                All Weeks
              </DropdownMenuItem>
              {referenceSets.map((set) => (
                <DropdownMenuItem
                  key={set.id}
                  onClick={() => setSelectedSetId(set.id)}
                >
                  {set.title}
                </DropdownMenuItem>
              ))}
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        {/* Masonry Grid */}
        {isLoading ? (
          <div className="flex justify-center py-12">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-accent" />
          </div>
        ) : artworks.length > 0 ? (
          <div className="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
            {artworks.map((artwork) => (
              <div key={artwork.id} className="break-inside-avoid">
                <ArtworkCard
                  artwork={artwork}
                  onClick={() => handleArtworkClick(artwork)}
                />
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-12 text-text-secondary">
            No artworks available
          </div>
        )}

        {/* Load More Trigger */}
        {hasNextPage && (
          <div
            ref={loadMoreRef}
            className="flex justify-center py-8"
          >
            {isFetchingNextPage ? (
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-accent" />
            ) : (
              <Button
                variant="secondary"
                onClick={() => fetchNextPage()}
              >
                Load More
              </Button>
            )}
          </div>
        )}
      </div>

      {/* Artwork Modal */}
      <ArtworkModal
        artwork={selectedArtwork}
        open={isModalOpen}
        onOpenChange={setIsModalOpen}
      />
    </div>
  );
}
