"use client";

import { useState } from "react";
// Using img instead of next/image to avoid optimization issues with external URLs
import { useReferenceSets, useReferenceSet } from "@/hooks/api";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import { UploadModal } from "@/components/upload/upload-modal";

export default function Home() {
  const [selectedSetId, setSelectedSetId] = useState<string | null>(null);
  const [isUploadModalOpen, setIsUploadModalOpen] = useState(false);
  const { data: setsData, isLoading: isLoadingSets } = useReferenceSets();
  const { data: selectedSet, isLoading: isLoadingSet } = useReferenceSet(
    selectedSetId || ""
  );

  const referenceSets = setsData?.data || [];
  const currentSet = selectedSet || referenceSets[0];

  return (
    <div className="flex flex-col items-center min-h-[calc(100vh-4rem)] px-4 py-8 relative">
      {/* Content */}
      <div className="relative z-10 w-full max-w-6xl mx-auto space-y-8">
        {/* Week Selector */}
        <div className="flex justify-center">
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="secondary" className="min-w-[200px]">
                {currentSet?.title || "Select Week"}
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

        {/* Reference Images */}
        {isLoadingSets || isLoadingSet ? (
          <div className="flex justify-center py-12">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-accent" />
          </div>
        ) : currentSet ? (
          <div className="space-y-6">
            <h2 className="text-2xl font-cinzel text-text-primary uppercase tracking-wider text-center">
              {currentSet.title}
            </h2>

            {/* Reference Images Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {currentSet.items.map((item) => (
                <div
                  key={item.id}
                  className="bg-bg-surface gothic-border rounded-sm overflow-hidden group"
                >
                  <div className="relative aspect-[4/3]">
                    <img
                      src={item.reference_image.cdn_url.replace("localhost:8080", "localhost:3000")}
                      alt={`Reference ${item.reference_image.id}`}
                      className="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                    />
                  </div>
                  <div className="p-3 border-t border-border bg-bg-surface-elevated">
                    <p className="text-sm font-cinzel text-text-primary uppercase tracking-wider text-center font-semibold">
                      {item.reference_image.category.name}
                    </p>
                  </div>
                </div>
              ))}
            </div>

            {/* Action Buttons */}
            <div className="flex flex-col sm:flex-row gap-4 justify-center items-center pt-8">
              <button 
                onClick={() => setIsUploadModalOpen(true)}
                className="px-8 py-3 border border-accent text-accent uppercase tracking-wider font-cinzel hover:glow-accent transition-all duration-300 min-h-[48px] min-w-[44px]"
              >
                УЧАСТВОВАТЬ
              </button>
              <button 
                disabled
                className="px-8 py-3 border border-border text-text-secondary uppercase tracking-wider font-cinzel opacity-50 cursor-not-allowed min-h-[48px] min-w-[44px]"
                title="Download feature coming soon"
              >
                СКАЧАТЬ РЕФЕРЕНСЫ
              </button>
            </div>
          </div>
        ) : (
          <div className="text-center py-12 text-text-secondary">
            No reference sets available
          </div>
        )}
      </div>

      {/* Upload Modal */}
      {currentSet && (
        <UploadModal
          open={isUploadModalOpen}
          onOpenChange={setIsUploadModalOpen}
          referenceSetId={currentSet.id}
        />
      )}
    </div>
  );
}
