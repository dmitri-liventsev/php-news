import React from 'react';
import CategoryPreview from '../Category/CategoryPreview';
import {useFetchNewsQuery} from "../../features/api/apiSlice";
import Loading from "../Util/Loading";

const TopNews: React.FC = () => {
    const { data: categories, error, isLoading } = useFetchNewsQuery();

    if (isLoading) return <Loading />;

    return (
        <div>
            {categories?.map(category => (
                <CategoryPreview key={category.id} category={category} />
            ))}
        </div>
    );
};

export default TopNews;
