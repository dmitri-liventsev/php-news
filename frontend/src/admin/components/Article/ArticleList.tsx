import React, {useEffect} from 'react';
import {useDispatch, useSelector} from "react-redux";
import {AppDispatch, RootState} from "../../store";
import {deleteArticle, fetchArticles, resetArticles} from "../../features/news/adminSlice";
import InfiniteScroll from "react-infinite-scroll-component";
import {CircularProgress} from "@mui/material";
import ArticleRow from "./ArticleRow";
import {Article} from "./index";
import TableHeader from "./TableHeader";

const ArticleList = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { articlesState } = useSelector(
        (state: RootState) => state.admin || { articles: [], hasMore: true, page: 1 }
    );

    let articles = [] as Article[];
    let currentPage = 1;
    let loading = true;
    let error = null;
    if ( articlesState) {
        articles = articlesState.articles;
        currentPage = articlesState.currentPage;
        loading = articlesState.loading;
        error = articlesState.error;
    }

    useEffect(() => {
        dispatch(resetArticles());
    }, [location.pathname, dispatch]);

    useEffect(() => {
        dispatch(fetchArticles({  page: 1 }));
    }, [dispatch]);

    const fetchMoreData = () => {
        dispatch(fetchArticles({  page: currentPage }));
    };

    if (error) return <div>Error: {error}</div>;

    return (
        <div>
            <TableHeader />
            <InfiniteScroll
                dataLength={articles.length}
                next={fetchMoreData}
                hasMore={articles.length / (currentPage-1) >= 10}
                loader={<></>}
            >
                {articles.map(article => (
                    <ArticleRow key={article.id} article={article}/>
                ))}
            </InfiniteScroll>

            {loading  && (<CircularProgress />)}
        </div>
    );
};

export default ArticleList;