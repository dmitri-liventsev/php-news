import React from 'react';
import { useFetchCategoriesQuery } from '../../features/api/apiSlice';
import { Box, List, Typography} from '@mui/material';
import CategoryRow from './CategoryRow';
import CategoryButton from "./CategoryButton";
import Loading from "../Util/Loading";

const CategoryList = () => {
    const { data: categories, error, isLoading, isFetching } = useFetchCategoriesQuery();

    if (isLoading) return <Loading />;
    const noCategories = categories?.length === 0 && !isFetching;
    return (
        <>
            <Box marginTop={'10px'}>
                <CategoryButton/>
            </Box>

            {isFetching  && (<Loading />)}

            {!isFetching  && (<List>
                {categories?.map(category => (
                    <CategoryRow key={category.id} category={category} />
                ))}
            </List>)}

            {noCategories && (
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    No categories found
                </Typography>
            )}
        </>

    );
};

export default CategoryList;