import React, { useState } from 'react';
import { TextField, Button, List } from '@mui/material';
import Comment from './Comment';
import { useFetchArticleCommentsQuery, usePostCommentMutation } from "../../features/api/apiSlice";
import Loading from "../Util/Loading";

interface ArticleCommentsProps {
    articleId: number;
}

const ArticleComments: React.FC<ArticleCommentsProps> = ({ articleId }) => {
    const { data: comments, error: commentsError, isFetching: commentsLoading } = useFetchArticleCommentsQuery(articleId);
    const [postComment, { error: postCommentError, isLoading: isPosting }] = usePostCommentMutation();

    const [author, setAuthor] = useState('');
    const [content, setContent] = useState('');
    const [formErrors, setFormErrors] = useState<{ author?: string; content?: string }>({});

    const handleCommentSubmit = async (e: React.FormEvent) => {
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

        try {
            await postComment({ id: 0, articleId, author, content }).unwrap();
            setAuthor('');
            setContent('');
            setFormErrors({});
        } catch (err) {
            console.error('Failed to post comment:', err);
        }
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
                <Button
                    type="submit"
                    variant="contained"
                    color="primary"
                    disabled={isPosting} // Button is disabled while posting
                >
                    {isPosting ? 'Submitting...' : 'Submit'}
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
