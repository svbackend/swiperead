import {BookAuthorPreviewEntity} from "./book-author-preview.entity";

export interface BookPreviewEntity {
  id: string;
  title: string;
  authors: BookAuthorPreviewEntity[];
}
