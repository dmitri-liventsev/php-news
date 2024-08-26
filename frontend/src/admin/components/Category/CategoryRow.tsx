import React, { useState } from 'react';
import {Box, Typography, IconButton, ListItem} from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import { useDispatch } from 'react-redux';
import { AppDispatch } from '../../store';
import {Category} from "./index";
import {deleteCategory} from "../../features/news/adminSlice";

interface Props {
    category: Category;
}

const ArticleRow: React.FC<Props> = ({ category }) => {
    const dispatch = useDispatch<AppDispatch>();
    const [loading, setLoading] = useState(false);

    const handleDelete = async () => {
        if (window.confirm('Are you sure?')) {
            setLoading(true);
            try {
                await dispatch(deleteCategory({ categoryId: category.id })).unwrap();
            } catch (error) {
                console.error('Failed to delete the article:', error);
            } finally {
                setLoading(false);
            }
        }
    };

    return (
        <ListItem
            key={category.id}
            sx={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                borderBottom: '1px solid #ddd',
                padding: '8px',
            }}
        >
            <Typography variant="body2" noWrap>
                {category.title}
            </Typography>
            <IconButton
                onClick={() => handleDelete()}
                disabled={loading === true}
            >
                <DeleteIcon />
            </IconButton>
        </ListItem>
    );
};

export default ArticleRow;
