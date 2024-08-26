import {CategoryPreview} from "../Category";

export interface Comment {
    id: number | null;
    articleId: number;
    author: string;
    content: string;
}

export interface Image {
    id: number;
    title: string;
    fileName: string;
    createdAt: string;
    updatedAt: string;
    deletedAt: string | null;
}

export interface Article {
    id: number;
    title: string;
    shortDescription: string;
    content: string;
    numberOfViews: number;
    isTop: boolean;
    image: Image,
    categories: CategoryPreview[],
    createdAt: string;
    updatedAt: string;
    deletedAt: string | null;
}

