import React from 'react';
import { useParams } from 'react-router-dom';
import { useFetchCommentsQuery } from '../../features/api/apiSlice';
import { CircularProgress, List } from '@mui/material';
import CommentRow from './CommentRow';

const CommentList = () => {
    const { article_id } = useParams();
    const articleId = Number(article_id);
    const { data: comments, error, isLoading } = useFetchCommentsQuery(articleId);

    if (isLoading) return <CircularProgress />;

    return (
        <List>
            {comments?.map(comment => (
                <CommentRow key={comment.id} comment={comment} />
            ))}
        </List>
    );
};

export default CommentList;
