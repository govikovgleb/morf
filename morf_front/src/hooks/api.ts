import { useQuery } from "@tanstack/react-query";
import { api } from "@/lib/api/client";
import type { ReferenceSet, PaginatedResponse, Artwork } from "@/types/api";

export function useReferenceSets() {
  return useQuery({
    queryKey: ["reference-sets"],
    queryFn: () => api.get<PaginatedResponse<ReferenceSet>>("/reference-sets"),
  });
}

export function useReferenceSet(id: string) {
  return useQuery({
    queryKey: ["reference-sets", id],
    queryFn: () => api.get<ReferenceSet>(`/reference-sets/${id}`),
    enabled: !!id,
  });
}

export function useArtworks(referenceSetId?: string) {
  return useQuery({
    queryKey: ["artworks", referenceSetId],
    queryFn: () => {
      const params = referenceSetId ? `?reference_set_id=${referenceSetId}` : "";
      return api.get<PaginatedResponse<Artwork>>(`/artworks${params}`);
    },
  });
}
