import React, { useState } from 'react';
import { Box, Typography, IconButton, ListItem } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import { useDeleteCommentMutation } from '../../features/api/apiSlice';
import { Comment } from './index';

interface Props {
    comment: Comment;
}

const CommentRow: React.FC<Props> = ({ comment }) => {
    const [deleteComment, { isLoading: isDeleting }] = useDeleteCommentMutation();
    const [loading, setLoading] = useState(false);

    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this comment?')) {
            setLoading(true);
            try {
                await deleteComment(comment.id).unwrap();
            } catch (error) {
                console.error('Failed to delete the comment:', error);
            } finally {
                setLoading(false);
            }
        }
    };

    return (
        <ListItem
            sx={{
                display: 'flex',
                alignItems: 'center',
                borderBottom: '1px solid #ddd',
                padding: '8px',
                width: '100%',
            }}
        >
            <Box
                sx={{
                    display: 'flex',
                    flex: 1,
                    justifyContent: 'space-between',
                }}
            >
                <Typography
                    variant="body2"
                    noWrap
                    sx={{ fontWeight: 'bold' }}
                >
                    {comment.author}
                </Typography>
                <Typography
                    variant="body2"
                    noWrap
                    sx={{ flex: 1, textAlign: 'left' }}
                >
                    {comment.content}
                </Typography>
                <IconButton
                    onClick={handleDelete}
                    disabled={loading || isDeleting}
                >
                    <DeleteIcon />
                </IconButton>
            </Box>
        </ListItem>
    );
};

export default CommentRow;