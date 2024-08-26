import {createSlice, createAsyncThunk, PayloadAction} from '@reduxjs/toolkit';
import {Article} from "../../components/Article";
import {Category} from "../../components/Category";
import {handleError} from "./errorHandler";

interface TokenState {
    token: string | null;
    error: string | null;
    loading: boolean;
}

interface ArticlesState {
    articles: Article[];
    error: string | null;
    loading: boolean;
    hasMore: boolean;
    currentPage: number;
}

interface CategoriesState {
    categories: Record<number, Category>;
    error: string | null;
    loading: boolean;
}

interface CategoriesState {
    categories: Record<number, Category>;
    error: string | null;
    loading: boolean;
}

interface CommentsState {
    categories: Record<number, Category>;
    error: string | null;
    loading: boolean;
}

interface RequestState {
    error: string | null;
    loading: boolean;
}
interface AdminState {
    tokenState: TokenState;
    articlesState: ArticlesState;
    categoriesState: CategoriesState;
    commentsState: CommentsState;
    deleteState: RequestState
}

const initialState: AdminState = {
    tokenState: {
        token: null,
        error: null,
        loading: false,
    },
    articlesState: {
        articles: [] as Article[],
        error: null,
        loading: false,
        hasMore: false,
        currentPage: 1,
    },
    categoriesState: {
        categories: {},
        error: null,
        loading: false,
    },
    commentsState: {
        categories: {},
        error: null,
        loading: false,
    },
    deleteState: {
        error: null,
        loading: false,
    }
};

export const login = createAsyncThunk<
    { token: string },
    { username: string; password: string },
    { rejectValue: string }
>(
    'auth/login',
    async ({ username, password }, thunkAPI) => {
        try {
            const response = await fetch('/admin/api/login_check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Login failed');
            }

            const data = await response.json();
            return { token: data.token };
        } catch (error) {
            return thunkAPI.rejectWithValue((error as Error).message);
        }
    }
);

export const fetchArticles = createAsyncThunk<
    { articles: Article[] },
    { page: number }
>(
    'news/fetchArticlesByCategory',
    async ({ page }) => {
        const token = localStorage.getItem('token');

        const response = await fetch(`/admin/api/articles/${page}`, {
            headers: {
                'Authorization': `Bearer ${token}`, // Добавляем токен в заголовки
                'Content-Type': 'application/json'
            }
        });

        const articles = await response.json();
        if (!response.ok) {
            handleError(response)
            throw new Error(articles.message || 'An unknown error occurred');
        }

        return { articles: articles };
    }
);

export const deleteArticle = createAsyncThunk<
    { articleId: number },
    { articleId: number },
    { rejectValue: string }
>(
    'articles/deleteArticle',
    async ({ articleId }, thunkAPI) => {
        try {
            const token = localStorage.getItem('token');

            const response = await fetch(`/admin/api/article/${articleId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                handleError(response)
                throw new Error('Failed to delete article');
            }

            return { articleId };
        } catch (error) {
            return thunkAPI.rejectWithValue((error as Error).message);
        }
    }
);

const adminSlice = createSlice({
    name: 'admin',
    initialState,
    reducers: {
        resetArticles: (state) => {
            state.articlesState = {
                articles: [],
                hasMore: true,
                currentPage: 1,
                loading: false,
                error: null,
            };
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(login.pending, (state) => {
                state.tokenState.loading = true;
                state.tokenState.error = null;
            })
            .addCase(login.fulfilled, (state, action) => {
                state.tokenState.loading = false;
                state.tokenState.token = action.payload.token;
                state.tokenState.error = null;
            })
            .addCase(login.rejected, (state, action) => {
                state.tokenState.loading = false;
                state.tokenState.error = action.payload || 'An error occurred';
            })

// Articles
            .addCase(fetchArticles.pending, (state, action) => {
                if (!state.articlesState) {
                    state.articlesState = {
                        articles: [],
                        hasMore: true,
                        currentPage: 1,
                        loading: true,
                        error: null,
                    };
                } else {
                    state.articlesState.loading = true;
                }
            })
            .addCase(fetchArticles.fulfilled, (state, action) => {
                const { articles } = action.payload;
                const articlesState = state.articlesState;
                articlesState.loading = false;
                articlesState.articles = [
                    ...articlesState.articles,
                    ...articles,
                ];
                articlesState.hasMore = articles.length === 10;
                articlesState.currentPage += 1;
            })
            .addCase(fetchArticles.rejected, (state, action) => {
                const categoryState = state.articlesState;
                if (categoryState) {
                    categoryState.loading = false;
                    categoryState.error = action.error.message || 'An unknown error occurred';
                }
            })
            .addCase(deleteArticle.pending, (state) => {
                state.deleteState.loading = true;
                state.deleteState.error = null;
            })
            .addCase(deleteArticle.fulfilled, (state, action: PayloadAction<{ articleId: number }>) => {
                state.deleteState.loading = false;
                state.articlesState.articles = state.articlesState.articles.filter(article => article.id !== action.payload.articleId);
            })
            .addCase(deleteArticle.rejected, (state, action) => {
                state.deleteState.loading = false;
                state.deleteState.error = action.payload || 'Failed to delete article';
            })

        ;
    },
});

export const { resetArticles } = adminSlice.actions;

export default adminSlice.reducer;
