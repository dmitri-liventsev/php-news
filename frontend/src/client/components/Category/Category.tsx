import React, { useEffect } from 'react';
import { useParams } from 'react-router-dom';
import InfiniteScroll from 'react-infinite-scroll-component';
import { useDispatch, useSelector } from 'react-redux';
import {AppDispatch, RootState} from '../../store';
import {fetchArticlesByCategory} from "../../features/news/newsSlice";
import {CategoryPreview} from "./index";
import {Article} from "../Article";
import {Box, CardMedia} from "@mui/material";
import ArticlePreview from "../Article/ArticlePreview";

const Category: React.FC = () => {
    const { categoryId } = useParams<{ categoryId: string }>();
    const categoryIdInt = Number(categoryId)
    const dispatch = useDispatch<AppDispatch>();
    const { categoryArticles } = useSelector(
        (state: RootState) => state.news || { articles: [], category: null, hasMore: true, page: 1 }
    );

    let articles = [] as Article[];
    let category = {} as CategoryPreview;
    let currentPage = 1;
    let loading = true;
    let error = null;

    if ( categoryArticles[categoryIdInt]) {
        articles = categoryArticles[categoryIdInt].articles;
        category = categoryArticles[categoryIdInt].category;
        currentPage = categoryArticles[categoryIdInt].currentPage;
        loading = categoryArticles[categoryIdInt].loading;
        error = categoryArticles[categoryIdInt].error;
    }

    useEffect(() => {
        if (categoryId) {
            dispatch(fetchArticlesByCategory({ categoryId: categoryIdInt, page: 1 }));
        }
    }, [categoryIdInt, dispatch]);

    const fetchMoreData = () => {
        if (categoryId) {
            dispatch(fetchArticlesByCategory({ categoryId: categoryIdInt, page: currentPage }));
        }
    };

    if (error) return <div>Error: {error}</div>;

    return (
        <div>
            <h1>{category.title}</h1>
            <InfiniteScroll
                dataLength={articles.length}
                next={fetchMoreData}
                hasMore={articles.length / (currentPage-1) >= 10}
                loader={<></>}
            >
                {articles.map(article => (
                    <ArticlePreview key={article.id} article={article} direction={"row"}/>
                ))}
            </InfiniteScroll>

            {loading  && (<h4>Loading...</h4>)}
        </div>
    );
};

export default Category;
