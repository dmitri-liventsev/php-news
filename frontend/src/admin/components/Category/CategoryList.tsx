import React from 'react';
import { useFetchCategoriesQuery } from '../../features/api/apiSlice';
import { CircularProgress, List } from '@mui/material';
import CategoryRow from './CategoryRow';

const CategoryList = () => {
    const { data: categories, error, isLoading } = useFetchCategoriesQuery();

    if (isLoading) return <CircularProgress />;

    return (
        <List>
            {categories?.map(category => (
                <CategoryRow key={category.id} category={category} />
            ))}
        </List>
    );
};

export default CategoryList;