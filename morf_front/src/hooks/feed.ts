import { useInfiniteQuery } from "@tanstack/react-query";
import { api } from "@/lib/api/client";
import type { Artwork, PaginatedResponse } from "@/types/api";

interface UseInfiniteArtworksOptions {
  referenceSetId?: string;
}

export function useInfiniteArtworks({ referenceSetId }: UseInfiniteArtworksOptions = {}) {
  return useInfiniteQuery({
    queryKey: ["artworks", "infinite", referenceSetId],
    queryFn: async ({ pageParam = 1 }) => {
      const params = new URLSearchParams();
      params.append("page", pageParam.toString());
      if (referenceSetId) {
        params.append("reference_set_id", referenceSetId);
      }
      
      return api.get<PaginatedResponse<Artwork>>(`/artworks?${params.toString()}`);
    },
    getNextPageParam: (lastPage) => {
      if (lastPage.current_page < lastPage.last_page) {
        return lastPage.current_page + 1;
      }
      return undefined;
    },
    initialPageParam: 1,
  });
}

export function useToggleLike() {
  const toggleLike = async (artworkId: string) => {
    return api.post<{ liked: boolean; likes_count: number }>(`/artworks/${artworkId}/likes`, {});
  };

  return { toggleLike };
}
