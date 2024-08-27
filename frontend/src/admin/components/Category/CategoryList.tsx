import React from 'react';
import { useFetchCategoriesQuery } from '../../features/api/apiSlice';
import {CircularProgress, List, Typography} from '@mui/material';
import CategoryRow from './CategoryRow';

const CategoryList = () => {
    const { data: categories, error, isLoading, isFetching } = useFetchCategoriesQuery();

    if (isLoading) return <CircularProgress />;
    const noCategories = categories?.length === 0 && !isFetching;
    return (
        <>
            <List>
                {categories?.map(category => (
                    <CategoryRow key={category.id} category={category} />
                ))}
            </List>

            {noCategories && (
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    No categories found
                </Typography>
            )}
        </>

    );
};

export default CategoryList;