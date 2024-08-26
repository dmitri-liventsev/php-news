import React from 'react';
import { useParams } from 'react-router-dom';

const CommentList = () => {
    const { article_id } = useParams();

    return <h1>CommentList Page for Article ID: {article_id}</h1>;
};

export default CommentList;