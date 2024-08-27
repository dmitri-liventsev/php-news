import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import { Category, CategoryPreview } from '../../components/Category';
import { Article, Comment } from '../../components/Article';

export const apiSlice = createApi({
    reducerPath: 'api',
    baseQuery: fetchBaseQuery({ baseUrl: '/api' }),
    tagTypes: ['Articles', 'Categories', 'Comments'],
    endpoints: (builder) => ({
        fetchNews: builder.query<Category[], void>({
            query: () => '/top-news',
            providesTags: ['Categories'],
        }),
        fetchArticlesByCategory: builder.query<{ articles: Article[]; category: CategoryPreview }, { categoryId: number; page: number }>({
            query: ({ categoryId, page }) => `/category/${categoryId}/articles/${page}`,
            providesTags: (result, error, { categoryId }) => [{ type: 'Articles', id: categoryId }],
        }),
        fetchArticle: builder.query<Article, number>({
            query: (articleId) => `/article/${articleId}`,
            providesTags: (result, error, articleId) => [{ type: 'Articles', id: articleId }],
        }),
        fetchArticleComments: builder.query<Comment[], number>({
            query: (articleId) => `/article/${articleId}/comments`,
            providesTags: (result, error, articleId) => [{ type: 'Comments', id: articleId }],
        }),
        postComment: builder.mutation<Comment, Comment>({
            query: (comment) => ({
                url: `/article/${comment.articleId}/comments`,
                method: 'POST',
                body: comment,
            }),
            invalidatesTags: (result, error, { articleId }) => [{ type: 'Comments', id: articleId }],
        }),
    }),
});

export const {
    useFetchNewsQuery,
    useFetchArticlesByCategoryQuery,
    useFetchArticleQuery,
    useFetchArticleCommentsQuery,
    usePostCommentMutation,
} = apiSlice;
