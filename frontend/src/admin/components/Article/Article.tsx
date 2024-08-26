import React from 'react';
import { useParams } from 'react-router-dom';

const Article = () => {
    const { article_id } = useParams();

    return <h1>Article Page - ID: {article_id}</h1>;
};

export default Article;