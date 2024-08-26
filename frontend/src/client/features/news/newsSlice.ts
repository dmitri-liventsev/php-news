import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import {Category, CategoryPreview} from '../../components/Category';
import { Article, Comment } from '../../components/Article';

interface ArticlesByCategoryState {
    articles: Article[];
    category: CategoryPreview;
    hasMore: boolean;
    currentPage: number;
    loading: boolean;
    error: string | null;
}

interface TopNews {
    categories: Category[];
    loading: boolean;
    error: string | null;
}

interface ArticleState {
    article: Article,
    loading: boolean,
    error: string | null,
}

interface CommentState {
    comments: Comment[];
    loading: boolean;
    error: string | null
}

interface NewsState {
    categoryArticles: Record<number, ArticlesByCategoryState>;
    topNews: TopNews;
    articles: Record<number, ArticleState>
    articlesComments: Record<number, CommentState>
}


const initialState: NewsState = {
    categoryArticles: {},
    topNews: {
        categories: [],
        loading: true,
        error: null
    } as TopNews,
    articles: {},
    articlesComments: {},
};

export const fetchNews = createAsyncThunk<Category[]>(
    'news/fetchNews',
    async () => {
        const response = await fetch('/api/top-news');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return await response.json();
    }
);

export const fetchArticlesByCategory = createAsyncThunk<
    { categoryId: number; articles: Article[]; category: CategoryPreview },
    { categoryId: number; page: number }
>(
    'news/fetchArticlesByCategory',
    async ({ categoryId, page }) => {
        const response = await fetch(`/api/category/${categoryId}/articles/${page}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return { categoryId, articles: data.articles , category: data.category};
    }
);

export const fetchArticle = createAsyncThunk<
    { articleId: number; article: Article },
    { articleId: number }
>(
    'news/fetchArticle',
    async ({ articleId }) => {
        const response = await fetch(`/api/article/${articleId}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const article = await response.json();
        return { articleId, article };
    }
);

export const fetchArticleComments = createAsyncThunk<
    { articleId: number; comments: Comment[] },
    { articleId: number; }
>(
    'news/fetchArticleComments',
    async ({ articleId }) => {
        const response = await fetch(`/api/article/${articleId}/comments`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const comments = await response.json();
        return { articleId, comments: comments};
    }
);

export const postComment = createAsyncThunk<
    { articleId: number; comment: Comment },
    Comment,
    { rejectValue: string }
>(
    'comments/postComment',
    async (comment, thunkAPI) => {
        try {
            const response = await fetch(`/api/article/${comment.articleId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(comment), // Передача comment как объект
            });


            const res = await response.json();
            if (!response.ok) {
                if (res.errors) {
                    const validationErrors = res.errors.map(
                        (error: { property: string; message: string }) => `${error.property}: ${error.message}`
                    ).join('\n');

                    return thunkAPI.rejectWithValue(validationErrors);
                }

                const error = res.message || 'Failed to post comment';
                return thunkAPI.rejectWithValue(error);
            }

            comment.id = res.id;
            return { articleId: comment.articleId, comment: comment };
        } catch (error) {
            return thunkAPI.rejectWithValue((error as Error).message);
        }
    }
);

const newsSlice = createSlice({
    name: 'news',
    initialState,
    reducers: {
        resetCategoryState: (state, action: PayloadAction<{ categoryId: number }>) => {
            const { categoryId } = action.payload;
            state.categoryArticles[categoryId] = {
                articles: [],
                category: {} as CategoryPreview,
                hasMore: true,
                currentPage: 1,
                loading: false,
                error: null,
            };
        },
    },
    extraReducers: (builder) => {
        builder

// Top news

            .addCase(fetchNews.pending, (state) => {
                state.topNews.loading = true;
                state.topNews.error = null;
            })
            .addCase(fetchNews.fulfilled, (state, action) => {
                state.topNews.loading = false;
                state.topNews.categories = action.payload;
            })
            .addCase(fetchNews.rejected, (state, action) => {
                state.topNews.loading = false;
                state.topNews.error = action.error.message || 'An unknown error occurred';
            })

// Article by category

            .addCase(fetchArticlesByCategory.pending, (state, action) => {
                const { categoryId } = action.meta.arg;
                if (!state.categoryArticles[categoryId]) {
                    state.categoryArticles[categoryId] = {
                        articles: [],
                        category: {} as CategoryPreview,
                        hasMore: true,
                        currentPage: 1,
                        loading: true,
                        error: null,
                    };
                } else {
                    state.categoryArticles[categoryId].loading = true;
                }
            })
            .addCase(fetchArticlesByCategory.fulfilled, (state, action) => {
                const { categoryId, articles, category } = action.payload;
                const categoryState = state.categoryArticles[categoryId];
                categoryState.loading = false;
                categoryState.articles = [
                    ...categoryState.articles,
                    ...articles,
                ];
                categoryState.category = category;
                categoryState.hasMore = articles.length === 10;
                categoryState.currentPage += 1;
            })
            .addCase(fetchArticlesByCategory.rejected, (state, action) => {
                const { categoryId } = action.meta.arg;
                const categoryState = state.categoryArticles[categoryId];
                if (categoryState) {
                    categoryState.loading = false;
                    categoryState.error = action.error.message || 'An unknown error occurred';
                }
            })

// Article

            .addCase(fetchArticle.pending, (state, action) => {
                const { articleId } = action.meta.arg;
                if (!state.articles[articleId]) {
                    state.articles[articleId] = {
                        article: {} as Article,
                        loading: true,
                        error: null,
                    };
                } else {
                    state.articles[articleId].loading = true;
                }
            })
            .addCase(fetchArticle.fulfilled, (state, action: PayloadAction<{ articleId: number; article: Article }>) => {
                const { article, articleId } = action.payload;
                const articleState = state.articles[articleId];
                if (articleState) {
                    articleState.loading = false;
                    articleState.article = article;
                    articleState.error = null;
                }
            })
            .addCase(fetchArticle.rejected, (state, action) => {
                const { articleId } = action.meta.arg;
                const articleState = state.articles[articleId];
                if (articleState) {
                    articleState.loading = false;
                    articleState.error = action.error.message || 'An unknown error occurred';
                }
            })

//Comments
            .addCase(fetchArticleComments.pending, (state, action) => {
                const { articleId } = action.meta.arg;
                if (!state.articlesComments[articleId]) {
                    state.articlesComments[articleId] = {
                        comments: [],
                        loading: true,
                        error: null,
                    };
                } else {
                    state.articlesComments[articleId].loading = true;
                }
            })
            .addCase(fetchArticleComments.fulfilled, (state, action) => {
                const { comments, articleId } = action.payload;
                const articleCommentsState = state.articlesComments[articleId];
                articleCommentsState.loading = false;
                articleCommentsState.comments = comments;
            })
            .addCase(fetchArticleComments.rejected, (state, action) => {
                const { articleId } = action.meta.arg;
                const articleCommentsState = state.articlesComments[articleId];
                if (articleCommentsState) {
                    articleCommentsState.loading = false;
                    articleCommentsState.error = action.error.message || 'An unknown error occurred';
                }
            })

            .addCase(postComment.pending, (state, action) => {
                const { articleId } = action.meta.arg;
                if (!state.articlesComments[articleId]) {
                    state.articlesComments[articleId] = {
                        comments: [],
                        loading: true,
                        error: null,
                    };
                } else {
                    state.articlesComments[articleId].loading = true;
                }
            })
            .addCase(postComment.fulfilled, (state, action: PayloadAction<{ articleId: number; comment: Comment }>) => {
                const { articleId, comment } = action.payload;
                const commentsState = state.articlesComments[articleId];
                if (commentsState) {
                    commentsState.loading = false;
                    commentsState.comments.push(comment);
                }
            })
            .addCase(postComment.rejected, (state, action) => {
                const { articleId } = action.meta.arg;
                const commentsState = state.articlesComments[articleId];
                if (commentsState) {
                    commentsState.loading = false;
                    commentsState.error = action.payload as string;
                }
            });
    },
});

export const { resetCategoryState } = newsSlice.actions;

export default newsSlice.reducer;
