import React from 'react';
import { Card, CardContent, CardMedia, Typography } from '@mui/material';
import { Article } from './index';
import {Link} from "react-router-dom";

interface Props {
    article: Article;
    maxWidth?: number | undefined;
    direction?: 'row' | 'column';
}

const ArticlePreview: React.FC<Props> = ({ article, maxWidth, direction = 'row' }) => {
    const titleLink = (<Link to={`/article/${article.id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
        <Typography gutterBottom variant="h5" component="div">
            {article.title}
        </Typography>
    </Link>);

    const shortDescription = (<Typography variant="body2" color="text.secondary">
        {article.shortDescription}
    </Typography>);

    return (
        <>
            {direction === 'row' ? (
                <Card sx={{ display: 'flex', width: '100%', mb: 2 }}>
                    <CardMedia
                        component="img"
                        sx={{ width: 150 }}
                        image={`/images/articles/${article.image.fileName}`}
                        alt={article.title}
                    />
                    <CardContent sx={{ flex: 1 }}>
                        {titleLink}
                        {shortDescription}
                    </CardContent>
                </Card>
            ) : (
                <Card sx={{ maxWidth: maxWidth, margin: 2 }}>
                    <CardMedia
                        component="img"
                        height="140"
                        image={"/images/articles/" + article.image.fileName}
                        alt={article.title}
                    />
                    <CardContent>
                        {titleLink}
                        {shortDescription}
                    </CardContent>
                </Card>
            )}
        </>
    );
};

export default ArticlePreview;
