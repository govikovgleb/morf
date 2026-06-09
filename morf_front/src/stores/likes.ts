import { create } from "zustand";
import { persist } from "zustand/middleware";

interface LikeStore {
  likedArtworks: string[];
  isLiked: (artworkId: string) => boolean;
  toggleLike: (artworkId: string) => void;
}

export const useLikeStore = create<LikeStore>()(
  persist(
    (set, get) => ({
      likedArtworks: [],
      isLiked: (artworkId) => get().likedArtworks.includes(artworkId),
      toggleLike: (artworkId) => {
        const { likedArtworks } = get();
        const newSet = new Set(likedArtworks);
        if (newSet.has(artworkId)) {
          newSet.delete(artworkId);
        } else {
          newSet.add(artworkId);
        }
        set({ likedArtworks: Array.from(newSet) });
      },
    }),
    {
      name: "liked-artworks",
    }
  )
);
