import React, {useEffect, useState} from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { TextField, Button, List, CircularProgress, Typography } from '@mui/material';
import Comment from './Comment';
import { AppDispatch, RootState } from '../../store';
import {fetchArticle, fetchArticleComments, postComment} from '../../features/news/newsSlice';
import { Comment as CommentType } from './';

interface ArticleCommentsProps {
    articleId: number;
}

const ArticleComments: React.FC<ArticleCommentsProps> = ({ articleId }) => {
    const dispatch = useDispatch<AppDispatch>();
    const { articlesComments } = useSelector((state: RootState) => state.news);

    let comments = [] as CommentType[];
    let loading = false;
    let error = null;
    if (articlesComments[articleId]) {
        comments = articlesComments[articleId].comments;
        loading = articlesComments[articleId].loading;
        error = articlesComments[articleId].error;
    }

    useEffect(() => {
        if (articleId) {
            dispatch(fetchArticleComments({ articleId: +articleId }));
        }
    }, [dispatch, articleId]);

    const [author, setAuthor] = useState('');
    const [content, setContent] = useState('');
    const [formErrors, setFormErrors] = useState<{ author?: string; content?: string }>({});

    const handleCommentSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        const errors: { author?: string; content?: string } = {};
        if (author.trim() === '') {
            errors.author = 'Author name cannot be empty.';
        } else if (author.length > 255) {
            errors.author = 'Author name cannot be longer than 255 characters.';
        } else if (author.length < 2) {
            errors.author = 'Author name must be at least 2 characters long.';
        }

        if (content.trim() === '') {
            errors.content = 'Comment cannot be empty.';
        }

        if (Object.keys(errors).length > 0) {
            setFormErrors(errors);
            return;
        }

        dispatch(postComment({ id: null, articleId, author, content }));
        setAuthor('');
        setContent('');
        setFormErrors({});
    };

    return (
        <div style={{ padding: '16px' }}>
            <form onSubmit={handleCommentSubmit} style={{ marginBottom: '16px' }}>
                <TextField
                    label="Author"
                    variant="outlined"
                    fullWidth
                    value={author}
                    onChange={(e) => setAuthor(e.target.value)}
                    style={{ marginBottom: '8px' }}
                    error={!!formErrors.author}
                    helperText={formErrors.author}
                />
                <TextField
                    label="Comment"
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
                <Button type="submit" variant="contained" color="primary">Submit</Button>
            </form>

            {loading && <CircularProgress />}
            {error && <Typography color="error">Error: {error}</Typography>}

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
