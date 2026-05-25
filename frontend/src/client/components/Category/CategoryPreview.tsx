import React from 'react';
import { Grid, Typography, Box } from '@mui/material';
import ChevronRightIcon from '@mui/icons-material/ChevronRight';
import ArticlePreview from '../Article/ArticlePreview';
import { Category as CategoryType } from './index';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

interface Props {
    category: CategoryType;
}

const CategoryPreview: React.FC<Props> = ({ category }) => {
    const { t } = useTranslation();

    return (
        <div>
            <Typography variant="h4" gutterBottom>
                <Box
                    component={Link}
                    to={`/category/${category.id}`}
                    sx={{
                        display: 'inline-flex',
                        alignItems: 'center',
                        gap: 0.5,
                        color: 'primary.main',
                        textDecoration: 'none',
                        transition: 'color 0.15s ease',
                        '& .chevron': {
                            transition: 'transform 0.15s ease',
                        },
                        '&:hover': {
                            textDecoration: 'underline',
                            color: 'primary.dark',
                            '& .chevron': {
                                transform: 'translateX(3px)',
                            },
                        },
                    }}
                >
                    {category.title}
                    <ChevronRightIcon className="chevron" fontSize="inherit" />
                </Box>
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
