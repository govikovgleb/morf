export interface Category {
  id: string;
  name: string;
  slug: string;
  sort_order: number;
  created_at: string;
  updated_at: string;
}

export interface ReferenceImage {
  id: string;
  category_id: string;
  cdn_url: string;
  storage_path: string;
  width: number;
  height: number;
  file_size_bytes: number;
  mime_type: string;
  uploaded_by: string;
  created_at: string;
  updated_at: string;
  category: Category;
}

export interface ReferenceSetItem {
  id: string;
  set_id: string;
  reference_image_id: string;
  created_at: string;
  updated_at: string;
  reference_image: ReferenceImage;
}

export interface ReferenceSet {
  id: string;
  title: string;
  week_start_date: string;
  is_published: boolean;
  published_at: string;
  created_by: string;
  created_at: string;
  updated_at: string;
  items: ReferenceSetItem[];
}

export interface PaginatedResponse<T> {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: Array<{
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
  }>;
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
}

export interface Artwork {
  id: string;
  user_id: string;
  reference_set_id: string;
  cdn_url: string;
  storage_path: string;
  width: number;
  height: number;
  file_size_bytes: number;
  mime_type: string;
  caption: string | null;
  author_nickname: string;
  status: string;
  likes_count: number;
  moderated_by: string | null;
  moderated_at: string | null;
  deleted_at: string | null;
  created_at: string;
  updated_at: string;
}
