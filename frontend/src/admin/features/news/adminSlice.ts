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
    deleteArticleState: RequestState
    deleteCategoryState: RequestState
    deleteCommentState: RequestState
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
    deleteArticleState: {
        error: null,
        loading: false,
    },
    deleteCategoryState: {
        error: null,
        loading: false,
    },
    deleteCommentState: {
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
    'admin/fetchArticles',
    async ({ page }) => {
        const token = localStorage.getItem('token');

        const response = await fetch(`/admin/api/articles/${page}`, {
            headers: {
                'Authorization': `Bearer ${token}`, // Добавляем токен в заголовки
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        if (!response.ok) {
            handleError(response)
            throw new Error(data.message || 'An unknown error occurred');
        }

        return { articles: data };
    }
);

export const deleteArticle = createAsyncThunk<
    { articleId: number },
    { articleId: number },
    { rejectValue: string }
>(
    'admin/deleteArticle',
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

export const fetchCategories = createAsyncThunk<
    { categories: Category[] },
    { }
>(
    'admin/fetchCategories',
    async ({  }) => {
        const token = localStorage.getItem('token');

        const response = await fetch(`/admin/api/category`, {
            headers: {
                'Authorization': `Bearer ${token}`, // Добавляем токен в заголовки
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        if (!response.ok) {
            handleError(response)
            throw new Error(data.message || 'An unknown error occurred');
        }

        return { categories: data };
    }
);

export const deleteCategory = createAsyncThunk<
    { categoryId: number },
    { categoryId: number },
    { rejectValue: string }
>(
    'admin/deleteCategory',
    async ({ categoryId }, thunkAPI) => {
        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`/admin/api/category/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                handleError(response)
                throw new Error('Failed to delete category');
            }

            return { categoryId };
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
                const articleState = state.articlesState;
                if (articleState) {
                    articleState.loading = false;
                    articleState.error = action.error.message || 'An unknown error occurred';
                }
            })
            .addCase(deleteArticle.pending, (state) => {
                state.deleteArticleState.loading = true;
                state.deleteArticleState.error = null;
            })
            .addCase(deleteArticle.fulfilled, (state, action: PayloadAction<{ articleId: number }>) => {
                state.deleteArticleState.loading = false;
                state.articlesState.articles = state.articlesState.articles.filter(article => article.id !== action.payload.articleId);
            })
            .addCase(deleteArticle.rejected, (state, action) => {
                state.deleteArticleState.loading = false;
                state.deleteArticleState.error = action.payload || 'Failed to delete article';
            })

// Categories
            .addCase(fetchCategories.pending, (state, action) => {
                if (!state.categoriesState) {
                    state.categoriesState = {
                        categories: [],
                        loading: true,
                        error: null,
                    };
                } else {
                    state.categoriesState.loading = true;
                }
            })
            .addCase(fetchCategories.fulfilled, (state, action) => {
                const { categories } = action.payload;
                const categoryState = state.categoriesState;
                categoryState.loading = false;
                categoryState.categories = categories.reduce(
                    (acc, category) => {
                        acc[category.id] = category;
                        return acc;
                    },
                    {} as Record<number, Category>
                );
            })
            .addCase(fetchCategories.rejected, (state, action) => {
                const categoryState = state.categoriesState;
                if (categoryState) {
                    categoryState.loading = false;
                    categoryState.error = action.error.message || 'An unknown error occurred';
                }
            })
            .addCase(deleteCategory.pending, (state) => {
                state.deleteCategoryState.loading = true;
                state.deleteCategoryState.error = null;
            })
            .addCase(deleteCategory.fulfilled, (state, action: PayloadAction<{ categoryId: number }>) => {
                state.deleteCategoryState.loading = false;
                const { categoryId } = action.payload;
                if (state.categoriesState.categories[categoryId]) {
                    delete state.categoriesState.categories[categoryId];
                }
            })
            .addCase(deleteCategory.rejected, (state, action) => {
                state.deleteCategoryState.loading = false;
                state.deleteCategoryState.error = action.payload || 'Failed to delete article';
            })


        ;
    },
});

export const { resetArticles } = adminSlice.actions;

export default adminSlice.reducer;
