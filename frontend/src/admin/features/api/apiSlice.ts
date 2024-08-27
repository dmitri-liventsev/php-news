import {createApi, FetchArgs, fetchBaseQuery} from '@reduxjs/toolkit/query/react';
import {Article} from "../../components/Article";
import {Category} from "../../components/Category";
import {Comment} from "../../components/Comment";

interface TokenResponse {
    token: string;
}

interface ArticlesResponse extends Array<Article> {}

interface CategoriesResponse extends Array<Category> {}
interface CommentsResponse extends Array<Comment> {}

interface LoginResponse {
    token: string;
}

const baseQuery = fetchBaseQuery({
    baseUrl: '/admin/api',
    prepareHeaders: (headers) => {
        const token = localStorage.getItem('token');
        if (token) {
            headers.set('Authorization', `Bearer ${token}`);
        }

        return headers;
    },
});

const baseQueryWithJwt = async (args: string | FetchArgs, api: any, extraOptions: any) => {
    let result = await baseQuery(args, api, extraOptions);

    if (result.error && result.error.status === 401) {
        // Remove token and redirect on 401
        localStorage.removeItem('token');
        window.location.href = '/admin';
    }

    return result;
};

export const apiSlice = createApi({
    reducerPath: 'api',
    baseQuery: baseQueryWithJwt,
    tagTypes: ['Category', 'Article', 'Comment'],
    endpoints: (builder) => ({
        login: builder.mutation<LoginResponse, { username: string; password: string }>({
            query: (credentials) => ({
                url: '/login_check',
                method: 'POST',
                body: credentials,
            }),
        }),
        fetchArticles: builder.query<ArticlesResponse, number>({
            query: (page) => `/articles/${page}`,
            providesTags: (result) => result ? [{ type: 'Article', id: 'LIST' }] : [],
        }),
        deleteArticle: builder.mutation<void, number>({
            query: (articleId) => ({
                url: `/article/${articleId}`,
                method: 'DELETE',
            }),
            invalidatesTags: [{ type: 'Article', id: 'LIST' }],
        }),
        fetchCategories: builder.query<CategoriesResponse, void>({
            query: () => '/category',
            providesTags: (result) => result ? [{ type: 'Category', id: 'LIST' }] : [],
        }),
        deleteCategory: builder.mutation<void, number>({
            query: (categoryId) => ({
                url: `/category/${categoryId}`,
                method: 'DELETE',
            }),
            invalidatesTags: [{ type: 'Category', id: 'LIST' }],
        }),
        fetchComments: builder.query<CommentsResponse, number>({
            query: (articleId) => `/article/${articleId}/comments`,
            providesTags: (result) => result ? [{ type: 'Comment', id: 'LIST' }] : [],
        }),
        deleteComment: builder.mutation<void, number>({
            query: (commentId) => ({
                url: `/comment/${commentId}`,
                method: 'DELETE',
            }),
            invalidatesTags: [{ type: 'Comment', id: 'LIST' }],
        }),
    }),
});

export const {
    useFetchArticlesQuery,
    useFetchCategoriesQuery,
    useFetchCommentsQuery,
    useDeleteArticleMutation,
    useDeleteCategoryMutation,
    useDeleteCommentMutation,
    useLoginMutation,
} = apiSlice;
