import React, { useEffect } from 'react';
import CategoryPreview from '../Category/CategoryPreview';
import {useDispatch, useSelector} from "react-redux";
import {AppDispatch, RootState} from "../../store";
import {fetchNews} from "../../features/news/newsSlice";
import {CircularProgress} from "@mui/material"; // Импорт интерфейса Category

const TopNews: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { topNews } = useSelector((state: RootState) => state.news);

    useEffect(() => {
        dispatch(fetchNews());
    }, [dispatch]);

    if (topNews.loading) return <CircularProgress />;
    if (topNews.error) return <div>Error: {topNews.error}</div>;

    return (
        <div>
            {topNews.categories.map(category => (
                <CategoryPreview key={category.id} category={category} />
            ))}
        </div>
    );
};

export default TopNews;
