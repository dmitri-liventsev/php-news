import React, { useState } from 'react';
import { TextField, Button, List } from '@mui/material';
import Comment from './Comment';
import { useFetchArticleCommentsQuery, usePostCommentMutation } from "../../features/api/apiSlice";
import Loading from "../Util/Loading";
import { useTranslation } from "react-i18next";

interface ArticleCommentsProps {
    articleId: number;
}

const ArticleComments: React.FC<ArticleCommentsProps> = ({ articleId }) => {
    const { t } = useTranslation();
    const { data: comments, error: commentsError, isFetching: commentsLoading } = useFetchArticleCommentsQuery(articleId);
    const [postComment, { error: postCommentError, isLoading: isPosting }] = usePostCommentMutation();

    const [author, setAuthor] = useState('');
    const [content, setContent] = useState('');
    const [formErrors, setFormErrors] = useState<{ author?: string; content?: string }>({});

    const handleCommentSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const errors: { author?: string; content?: string } = {};
        if (author.trim() === '') {
            errors.author = t('formErrors.authorEmpty');
        } else if (author.length > 255) {
            errors.author = t('formErrors.authorTooLong');
        } else if (author.length < 2) {
            errors.author = t('formErrors.authorTooShort');
        }

        if (content.trim() === '') {
            errors.content = t('formErrors.commentEmpty');
        }

        if (Object.keys(errors).length > 0) {
            setFormErrors(errors);
            return;
        }

        try {
            await postComment({ id: 0, articleId, author, content }).unwrap();
            setAuthor('');
            setContent('');
            setFormErrors({});
        } catch (err) {
            console.error(t('formErrors.submitFailed'), err);
        }
    };

    return (
        <div style={{ padding: '16px' }}>
            <form onSubmit={handleCommentSubmit} style={{ marginBottom: '16px' }}>
                <TextField
                    label={t('author')}
                    variant="outlined"
                    fullWidth
                    value={author}
                    onChange={(e) => setAuthor(e.target.value)}
                    style={{ marginBottom: '8px' }}
                    error={!!formErrors.author}
                    helperText={formErrors.author}
                />
                <TextField
                    label={t('comment')}
                    variant="outlined"
                    multiline
                    rows={4}
                    fullWidth
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    style={{ marginBottom: '8px' }}
                    error={!!formErrors.content}
                    helperText={formErrors.content}
                />
                <Button
                    type="submit"
                    variant="contained"
                    color="primary"
                    disabled={isPosting}
                >
                    {isPosting ? t('submitting') : t('submit')}
                </Button>
            </form>

            {commentsLoading && <Loading />}

            {comments && (
                <List>
                    {comments.map((comment) => (
                        <Comment key={comment.id} comment={comment} />
                    ))}
                </List>
            )}
        </div>
    );
};

export default ArticleComments;
