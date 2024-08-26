import React from 'react';
import { useParams } from 'react-router-dom';

const Category = () => {
    const { categoryId } = useParams();

    return <h1>Category Page {categoryId ? `- ID: ${categoryId}` : ''}</h1>;
};

export default Category;