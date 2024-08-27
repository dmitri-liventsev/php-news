import React from 'react';
import { useFetchCategoriesQuery } from '../../features/api/apiSlice';
import { Box, List, Typography } from '@mui/material';
import CategoryRow from './CategoryRow';
import CategoryButton from "./CategoryButton";
import Loading from "../Util/Loading";
import { useTranslation } from 'react-i18next';

const CategoryList: React.FC = () => {
    const { t } = useTranslation();
    const { data: categories, error, isLoading, isFetching } = useFetchCategoriesQuery();

    if (isLoading) return <Loading />;
    const noCategories = categories?.length === 0 && !isFetching;

    return (
        <>
            <Box marginTop={'10px'}>
                <CategoryButton />
            </Box>

            {isFetching && <Loading />}

            {!isFetching && (
                <List>
                    {categories?.map(category => (
                        <CategoryRow key={category.id} category={category} />
                    ))}
                </List>
            )}

            {noCategories && (
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    {t('category.noCategoriesFound')}
                </Typography>
            )}
        </>
    );
};

export default CategoryList;
