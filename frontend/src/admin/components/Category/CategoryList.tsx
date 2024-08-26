import React, {useEffect} from 'react';
import {useDispatch, useSelector} from "react-redux";
import {AppDispatch, RootState} from "../../store";
import {fetchCategories,} from "../../features/news/adminSlice";
import {Category} from "./index";
import {CircularProgress, List,} from "@mui/material";
import CategoryRow from "./CategoryRow";

const CategoryList = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { categoriesState } = useSelector(
        (state: RootState) => state.admin || { articles: [], hasMore: true, page: 1 }
    );

    let categories = {} as Record<number, Category>;
    let loading = true;
    let error = null;
    if ( categoriesState) {
        categories = categoriesState.categories;
        loading = categoriesState.loading;
        error = categoriesState.error;
    }

    useEffect(() => {
        dispatch(fetchCategories({}));
    }, [dispatch]);

    const fetchMoreData = () => {
        dispatch(fetchCategories({}));
    };

    if (error) return <div>Error: {error}</div>;
    if (loading) return <CircularProgress />;

    const categoryArray = Object.values(categories);
    return (
        <List>
            {categoryArray.map((category) => (
                <CategoryRow category={category} />
            ))}
        </List>
    );
};

export default CategoryList;