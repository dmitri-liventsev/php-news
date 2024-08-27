import React, { useState, useEffect } from 'react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { useFetchArticlesQuery } from '../../features/api/apiSlice';
import {Box, Button, CircularProgress, List, Typography} from '@mui/material';
import ArticleRow from './ArticleRow';
import Loading from "../Util/Loading";
import CategoryButton from "../Category/CategoryButton";
import {Link} from "react-router-dom";

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

    const noArticles = articles?.length === 0 && !isFetching && page === 1;

    return (
        <>
            <Box marginTop={'10px'}>
                <Link to="/admin/articles/create">
                    <Button
                        variant="contained"
                        color="primary"
                    >
                        Create Article
                    </Button>
                </Link>
            </Box>

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

            {noArticles && (
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    No articles found
                </Typography>
            )}

            {isFetching  && (<Loading />)}
        </>
    );
};

export default ArticleList;
