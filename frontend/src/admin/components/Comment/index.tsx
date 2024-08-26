export interface Comment {
    id: number | null;
    articleId: number;
    author: string;
    content: string;
}