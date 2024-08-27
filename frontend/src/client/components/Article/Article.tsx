import React from 'react';
import { useParams, Link } from 'react-router-dom';
import { Card, CardContent, Typography, CardMedia, Box } from '@mui/material';
import ArticleComments from './ArticleComments';
import { useFetchArticleQuery } from "../../features/api/apiSlice";
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
                            <Box mt={2}>
                                <Typography variant="h6">{t('categories')}</Typography>
                                <Box
                                    display="flex"
                                    flexWrap="wrap"
                                    gap={1}
                                    mt={1}
                                >
                                    {article.categories.map(category => (
                                        <Link
                                            key={category.id}
                                            to={`/category/${category.id}`}
                                            style={{
                                                textDecoration: 'none',
                                                color: '#1976d2',
                                                fontSize: '1rem',
                                                marginRight: '10px'
                                            }}
                                        >
                                            {category.title}
                                        </Link>
                                    ))}
                                </Box>
                            </Box>
                        </CardContent>
                    </Card>
                    <ArticleComments articleId={articleIdInt} />
                </>
            )}
        </div>
    );
};

export default Article;
