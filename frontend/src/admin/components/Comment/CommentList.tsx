import React from 'react';
import { useParams } from 'react-router-dom';
import { useFetchCommentsQuery } from '../../features/api/apiSlice';
import { CircularProgress, List, Typography } from '@mui/material';
import CommentRow from './CommentRow';
import Loading from "../Util/Loading";
import { useTranslation } from 'react-i18next';

const CommentList = () => {
    const { article_id } = useParams();
    const articleId = Number(article_id);
    const { data: comments, error, isLoading, isFetching } = useFetchCommentsQuery(articleId);
    const { t } = useTranslation();

    if (isLoading) return <Loading />;

    const noComments = comments?.length === 0 && !isFetching;

    return (
        <>
            <List>
                {comments?.map(comment => (
                    <CommentRow key={comment.id} comment={comment} />
                ))}
            </List>

            {noComments && (
                <Typography variant="h6" align="center" sx={{ mt: 2 }}>
                    {t('comments.noCommentsFound')}
                </Typography>
            )}
        </>
    );
};

export default CommentList;
