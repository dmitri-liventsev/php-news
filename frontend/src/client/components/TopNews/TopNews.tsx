import React, { useEffect } from 'react';
import CategoryPreview from '../Category/CategoryPreview';
import {useDispatch, useSelector} from "react-redux";
import {AppDispatch, RootState} from "../../store";
import {fetchNews} from "../../features/news/newsSlice"; // Импорт интерфейса Category

const TopNews: React.FC = () => {
    const dispatch = useDispatch<AppDispatch>();
    const { news, loading, error } = useSelector((state: RootState) => state.news);

    useEffect(() => {
        dispatch(fetchNews());
    }, [dispatch]);

    if (loading) return <h4>Loading...</h4>;
    if (error) return <div>Error: {error}</div>;

    return (
        <div>
            {news.map(category => (
                <CategoryPreview key={category.id} category={category} />
            ))}
        </div>
    );
};

export default TopNews;
