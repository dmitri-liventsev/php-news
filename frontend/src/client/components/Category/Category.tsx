import React, { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import InfiniteScroll from 'react-infinite-scroll-component';
import { Article } from '../Article';
import { Typography } from '@mui/material';
import ArticlePreview from '../Article/ArticlePreview';
import { useFetchArticlesByCategoryQuery } from '../../features/api/apiSlice';
import Loading from '../Util/Loading';
import { useTranslation } from 'react-i18next';

const Category: React.FC = () => {
    const { t } = useTranslation();
    const { categoryId } = useParams<{ categoryId: string }>();
    const categoryIdInt = Number(categoryId);
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(true);
    const [articles, setArticles] = useState<Article[]>([]);

    const { data, error, isFetching, refetch } = useFetchArticlesByCategoryQuery(
        { categoryId: categoryIdInt, page },
        { skip: !categoryIdInt }
    );

    useEffect(() => {
        // Reset state and fetch data when categoryId changes
        setArticles([]);
        setPage(1);
        setHasMore(true);
        refetch();
    }, [categoryId, refetch]);

    useEffect(() => {
        if (data?.articles) {
            setArticles(prevArticles => [...prevArticles, ...data.articles]);
            // Check if there's more data to load
            setHasMore(data.articles.length >= 10);
        }
    }, [data]);

    const fetchMoreData = () => {
        if (hasMore) {
            setPage(prevPage => prevPage + 1);
        }
    };

    const noArticles = articles.length === 0 && !isFetching && page === 1;

    return (
        <div>
            <Typography variant="h4" gutterBottom>{data?.category.title}</Typography>
            <InfiniteScroll
                dataLength={articles.length}
                next={fetchMoreData}
                hasMore={hasMore}
                loader={<></>}
            >
                {articles.map((article: Article) => (
                    <ArticlePreview key={article.id} article={article} direction={"row"} />
                ))}
            </InfiniteScroll>

            {isFetching && <Loading />}
            {noArticles && <Typography variant="h6">{t('noArticles')}</Typography>}
        </div>
    );
};

export default Category;
