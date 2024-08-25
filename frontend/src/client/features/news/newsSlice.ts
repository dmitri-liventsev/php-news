import { createSlice, createAsyncThunk, PayloadAction } from '@reduxjs/toolkit';
import {Category, CategoryPreview} from '../../components/Category';
import { Article } from '../../components/Article';

interface ArticlesByCategoryState {
    articles: Article[];
    category: CategoryPreview;
    hasMore: boolean;
    currentPage: number;
    loading: boolean;
    error: string | null;
}

interface NewsState {
    news: Category[];
    categoryArticles: Record<number, ArticlesByCategoryState>;
    loading: boolean;
    error: string | null;
}

const initialState: NewsState = {
    news: [],
    categoryArticles: {},
    loading: false,
    error: null,
};

export const fetchNews = createAsyncThunk<Category[]>(
    'news/fetchNews',
    async () => {
        const response = await fetch('http://localhost:8080/api/top-news');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data;
    }
);

export const fetchArticlesByCategory = createAsyncThunk<
    { categoryId: number; articles: Article[]; category: CategoryPreview },
    { categoryId: number; page: number }
>(
    'news/fetchArticlesByCategory',
    async ({ categoryId, page }) => {
        const response = await fetch(`http://localhost:8080/api/category/${categoryId}/articles/${page}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return { categoryId, articles: data.articles , category: data.category};
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
            .addCase(fetchNews.pending, (state) => {
                state.loading = true;
                state.error = null;
            })
            .addCase(fetchNews.fulfilled, (state, action) => {
                state.loading = false;
                state.news = action.payload;
            })
            .addCase(fetchNews.rejected, (state, action) => {
                state.loading = false;
                state.error = action.error.message || 'An unknown error occurred';
            })


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
            });
    },
});

export const { resetCategoryState } = newsSlice.actions;

export default newsSlice.reducer;
