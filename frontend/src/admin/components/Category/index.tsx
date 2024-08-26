import { Article } from '../Article';

export interface Category {
    id: number;
    title: string;
    createdAt: string;
    updatedAt: string;
    deletedAt: string | null;
    articles: Article[];
}

export interface CategoryPreview {
    id: number,
    title: string
}