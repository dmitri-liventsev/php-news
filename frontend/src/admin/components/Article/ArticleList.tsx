import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { useFetchArticlesQuery } from '../../features/api/apiSlice';
import { CircularProgress, List } from '@mui/material';
import ArticleRow from './ArticleRow';

const ArticleList: React.FC = () => {
    const [page, setPage] = useState(1);
    const { data: articles, error, isLoading, isFetching } = useFetchArticlesQuery(page);
    const hasMore = articles != undefined && articles?.length >= 10;
    const fetchMoreData = () => {
        if (hasMore) {
            setPage(prevPage => prevPage + 1);
        }
    };

    useEffect(() => {
        setPage(1);
    }, []);

    return (
        <>
            <InfiniteScroll
                dataLength={articles?.length || 0}
                next={fetchMoreData}
                hasMore={hasMore}
                loader={<></>}
                endMessage={<></>}
            >
                <List>
                    {articles?.map(article => (
                        <ArticleRow key={article.id} article={article} />
                    ))}
                </List>
            </InfiniteScroll>

            {isLoading  && (<CircularProgress />)}
        </>
    );
};

export default ArticleList;
