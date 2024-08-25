import React from 'react';
import { useParams } from 'react-router-dom';

const Article = () => {
    const { articleId } = useParams();

    return (
        <div>
            <h1>Article ID: {articleId}</h1>
            {/* Здесь будет содержание статьи */}
        </div>
    );
};

export default Article;