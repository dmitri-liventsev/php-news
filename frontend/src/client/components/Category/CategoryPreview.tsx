import React from 'react';
import { Grid, Typography } from '@mui/material';
import ArticlePreview from '../Article/ArticlePreview';
import { Category as CategoryType } from './index';
import { Link } from 'react-router-dom';

interface Props {
    category: CategoryType;
}

const CategoryPreview: React.FC<Props> = ({ category }) => {
    return (
        <div>
            <Typography variant="h4" gutterBottom>
                <Link to={`/category/${category.id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
                    {category.title}
                </Link>
            </Typography>
            <Grid container spacing={2}>
                {category.articles.slice(0, 3).map(article => (
                    <Grid item xs={12} sm={4} key={article.id}>
                        <ArticlePreview article={article} maxWidth={345} direction={'column'}/>
                    </Grid>
                ))}
            </Grid>
        </div>
    );
};

export default CategoryPreview;