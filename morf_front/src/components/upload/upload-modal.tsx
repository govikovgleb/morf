"use client";

import { useState, useCallback } from "react";
import { useDropzone } from "react-dropzone";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { toast } from "sonner";
import { useAuthStore } from "@/stores/auth";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { AuthModal } from "@/components/auth/auth-modal";

const uploadSchema = z.object({
  caption: z.string().max(1000, "Caption must be at most 1000 characters").optional(),
});

type UploadForm = z.infer<typeof uploadSchema>;

interface UploadModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  referenceSetId: string;
}

export function UploadModal({ open, onOpenChange, referenceSetId }: UploadModalProps) {
  const [file, setFile] = useState<File | null>(null);
  const [preview, setPreview] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [isAuthModalOpen, setIsAuthModalOpen] = useState(false);
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const token = useAuthStore((state) => state.token);

  const form = useForm<UploadForm>({
    resolver: zodResolver(uploadSchema),
  });

  const onDrop = useCallback((acceptedFiles: File[]) => {
    if (acceptedFiles.length > 0) {
      const selectedFile = acceptedFiles[0];
      setFile(selectedFile);
      setPreview(URL.createObjectURL(selectedFile));
    }
  }, []);

  const { getRootProps, getInputProps, isDragActive, isDragReject } = useDropzone({
    onDrop,
    accept: {
      'image/*': ['.png', '.jpg', '.jpeg', '.gif', '.webp']
    },
    maxFiles: 1,
    maxSize: 20 * 1024 * 1024, // 20MB
  });

  const handleRemoveFile = () => {
    setFile(null);
    setPreview(null);
  };

  const handleUpload = async (data: UploadForm) => {
    if (!isAuthenticated) {
      setIsAuthModalOpen(true);
      return;
    }

    if (!file) {
      toast.error("Please select an image to upload");
      return;
    }

    setIsLoading(true);

    try {
      const formData = new FormData();
      formData.append("image", file);
      formData.append("reference_set_id", referenceSetId);
      if (data.caption) {
        formData.append("caption", data.caption);
      }

      const response = await fetch("/api/artworks", {
        method: "POST",
        headers: {
          "X-Device-Token": token || "",
        },
        body: formData,
      });

      if (!response.ok) {
        const error = await response.json().catch(() => ({}));
        throw new Error(error.message || "Upload failed");
      }

      toast.success("Работа отправлена на модерацию");
      onOpenChange(false);
      setFile(null);
      setPreview(null);
      form.reset();
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Upload failed");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <>
      <Dialog open={open} onOpenChange={onOpenChange}>
        <DialogContent className="sm:max-w-[500px] bg-bg-surface border-border">
          <DialogHeader>
            <DialogTitle className="text-center font-cinzel text-xl tracking-wider text-accent">
              ЗАГРУЗИТЬ РАБОТУ
            </DialogTitle>
          </DialogHeader>

          <form onSubmit={form.handleSubmit(handleUpload)} className="space-y-6">
            {/* Dropzone */}
            {!file ? (
              <div
                {...getRootProps()}
                className={`border-2 border-dashed rounded-sm p-8 text-center cursor-pointer transition-all duration-300 ${
                  isDragActive
                    ? "border-accent bg-accent/10 glow-accent"
                    : "border-border hover:border-border-hover"
                } ${isDragReject ? "border-red-600 bg-red-900/10" : ""}`}
              >
                <input {...getInputProps()} />
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="48"
                  height="48"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  strokeWidth="1"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  className="mx-auto mb-4 text-text-secondary"
                >
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                  <polyline points="17 8 12 3 7 8" />
                  <line x1="12" y1="3" x2="12" y2="15" />
                </svg>
                <p className="text-text-secondary font-inter">
                  {isDragActive
                    ? "Drop the image here"
                    : "Drag and drop an image here, or click to select"}
                </p>
                <p className="text-text-secondary/60 text-sm mt-2">
                  PNG, JPG, GIF up to 20MB
                </p>
              </div>
            ) : (
              <div className="relative bg-bg-primary rounded-sm overflow-hidden">
                {preview && (
                  <img
                    src={preview}
                    alt="Preview"
                    className="w-full h-48 object-cover"
                  />
                )}
                <div className="absolute top-2 right-2">
                  <button
                    type="button"
                    onClick={handleRemoveFile}
                    className="p-1 bg-bg-primary/80 rounded-sm text-text-secondary hover:text-accent transition-colors"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="20"
                      height="20"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    >
                      <line x1="18" y1="6" x2="6" y2="18" />
                      <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                  </button>
                </div>
                <div className="p-3 border-t border-border">
                  <p className="text-sm text-text-secondary truncate">{file.name}</p>
                  <p className="text-xs text-text-secondary/60">
                    {(file.size / 1024 / 1024).toFixed(2)} MB
                  </p>
                </div>
              </div>
            )}

            {/* Caption */}
            <div>
              <label className="text-sm font-cinzel text-text-secondary uppercase tracking-wider">
                Caption (optional)
              </label>
              <Input
                placeholder="Add a description..."
                {...form.register("caption")}
                className="mt-1"
              />
              {form.formState.errors.caption && (
                <p className="text-red-400 text-xs mt-1">
                  {form.formState.errors.caption.message}
                </p>
              )}
            </div>

            {/* Submit */}
            <Button
              type="submit"
              variant="primary"
              className="w-full"
              disabled={isLoading || !file}
            >
              {isLoading ? "Uploading..." : "ОТПРАВИТЬ"}
            </Button>
          </form>
        </DialogContent>
      </Dialog>

      <AuthModal open={isAuthModalOpen} onOpenChange={setIsAuthModalOpen} />
    </>
  );
}
