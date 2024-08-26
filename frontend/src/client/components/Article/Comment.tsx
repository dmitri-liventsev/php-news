import React from 'react';
import { ListItem, ListItemText } from '@mui/material';
import { Comment as CommentType } from './';

interface CommentProps {
    comment: CommentType;
}

const Comment: React.FC<CommentProps> = ({ comment }) => {
    return (
        <ListItem>
            <ListItemText
                primary={comment.author}
                secondary={comment.content}
            />
        </ListItem>
    );
};

export default Comment;
