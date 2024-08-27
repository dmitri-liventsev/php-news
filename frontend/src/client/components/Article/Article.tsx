import React from 'react';
import { useParams } from 'react-router-dom';
import { Card, CardContent, Typography, CircularProgress, CardMedia } from '@mui/material';
import ArticleComments from './ArticleComments';
import { useFetchArticleCommentsQuery, useFetchArticleQuery } from "../../features/api/apiSlice";
import Loading from "../Util/Loading";
import { useTranslation } from "react-i18next";

const PLACEHOLDER_IMAGE = '/images/placeholder.png';

const Article: React.FC = () => {
    const { t } = useTranslation();
    const { articleId } = useParams<{ articleId: string }>();
    const articleIdInt = Number(articleId);
    const { data: article, error: articleError, isLoading: articleLoading } = useFetchArticleQuery(articleIdInt);

    if (articleLoading) return <Loading />;

    const imageUrl = article && article.image && article.image.fileName != null
        ? `/images/articles/${article.image.fileName}`
        : PLACEHOLDER_IMAGE;

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
                                    image={imageUrl}
                                    alt={article.title}
                                />
                            )}
                            <Typography variant="h4">{article.title}</Typography>
                            <Typography variant="body1" style={{ marginBottom: '16px' }}>{article.shortDescription}</Typography>
                            <Typography variant="body2" color="textSecondary">
                                {t('publishedOn')}: {new Date(article.createdAt).toLocaleDateString()}
                            </Typography>
                        </CardContent>
                    </Card>
                    <ArticleComments articleId={articleIdInt} />
                </>
            )}
        </div>
    );
};

export default Article;
