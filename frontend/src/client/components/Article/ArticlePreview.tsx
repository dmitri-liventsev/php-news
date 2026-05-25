import React from 'react';
import { Card, CardActionArea, CardContent, CardMedia, Typography } from '@mui/material';
import { Link } from 'react-router-dom';
import { Article } from './index';
import { useTranslation } from 'react-i18next';

const PLACEHOLDER_IMAGE = '/images/placeholder.png';

interface Props {
    article: Article;
    maxWidth?: number;
    direction?: 'row' | 'column';
}

const hoverSx = {
    transition: 'transform 0.18s ease, box-shadow 0.18s ease',
    cursor: 'pointer',
    '&:hover': {
        transform: 'translateY(-3px)',
        boxShadow: 6,
    },
};

const ArticlePreview: React.FC<Props> = ({ article, maxWidth, direction = 'row' }) => {
    const { t } = useTranslation();

    const title = (
        <Typography gutterBottom variant="h5" component="div">
            {article.title}
        </Typography>
    );

    const shortDescription = (
        <Typography variant="body2" color="text.secondary">
            {article.shortDescription}
        </Typography>
    );

    const imageUrl = article.image && article.image.fileName != null ? `/images/articles/${article.image.fileName}` : PLACEHOLDER_IMAGE;
    const href = `/article/${article.id}`;

    return direction === 'row' ? (
        <Card sx={{ width: '100%', mb: 2, ...hoverSx }}>
            <CardActionArea
                component={Link}
                to={href}
                sx={{ display: 'flex', alignItems: 'stretch', justifyContent: 'flex-start' }}
            >
                <CardMedia
                    component="img"
                    sx={{ width: 150 }}
                    image={imageUrl}
                    alt={article.title}
                />
                <CardContent sx={{ flex: 1 }}>
                    {title}
                    {shortDescription}
                </CardContent>
            </CardActionArea>
        </Card>
    ) : (
        <Card sx={{ maxWidth, margin: 2, ...hoverSx }}>
            <CardActionArea component={Link} to={href}>
                <CardMedia
                    component="img"
                    height="140"
                    image={imageUrl}
                    alt={article.title}
                />
                <CardContent>
                    {title}
                    {shortDescription}
                </CardContent>
            </CardActionArea>
        </Card>
    );
};

export default ArticlePreview;
