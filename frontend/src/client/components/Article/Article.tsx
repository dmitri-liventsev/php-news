import React, { useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {Card, CardContent, Typography, Avatar, CircularProgress, CardMedia} from '@mui/material';
import {AppDispatch, RootState} from "../../store";
import {fetchArticle} from "../../features/news/newsSlice";
import {Article} from "./index";
import ArticleComments from "./ArticleComments";

const Article: React.FC = () => {
    const { articleId } = useParams<{ articleId: string }>();
    const articleIdInt = Number(articleId);
    const dispatch = useDispatch<AppDispatch>();

    const { articles } = useSelector((state: RootState) => state.news);

    let article = {} as Article;
    let loading = true;
    let error = null;
    if (articles[articleIdInt]) {
        article = articles[articleIdInt].article;
        loading = articles[articleIdInt].loading;
        error = articles[articleIdInt].error;
    }

    useEffect(() => {
        if (articleId) {
            dispatch(fetchArticle({ articleId: +articleId }));
        }
    }, [dispatch, articleId]);

    if (loading) return <CircularProgress />;
    if (error) return <Typography color="error">Error: {error}</Typography>;

    return (
        <div style={{ padding: '16px' }}>
            {article && (
                <>
                    <Card style={{ marginBottom: '16px' }}>
                        <CardContent>
                            {article.image && (
                                <CardMedia
                                    component="img"
                                    height="300"
                                    image={"/images/articles/" + article.image.fileName}
                                    alt={article.title}
                                />
                            )}

                            <Typography variant="h4">{article.title}</Typography>
                            <Typography variant="body1" style={{ marginBottom: '16px' }}>{article.shortDescription}</Typography>
                            <Typography variant="body2" color="textSecondary">Published on: {new Date(article.createdAt).toLocaleDateString()}</Typography>
                        </CardContent>
                    </Card>
                    <ArticleComments articleId={articleIdInt} />
                </>
            )}
        </div>
    );
};

export default Article;
