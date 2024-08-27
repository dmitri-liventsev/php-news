import React, { useState } from 'react';
import { Box, Typography, IconButton, ListItem } from '@mui/material';
import DeleteIcon from '@mui/icons-material/Delete';
import { useDeleteCategoryMutation } from '../../features/api/apiSlice'; // Импорт хука для удаления категории
import { Category } from './index';
import CategoryButton from "./CategoryButton";

interface Props {
    category: Category;
}

const CategoryRow: React.FC<Props> = ({ category }) => {
    const [deleteCategory, { isLoading: isDeleting }] = useDeleteCategoryMutation(); // Использование хука для удаления
    const [loading, setLoading] = useState(false);

    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this category?')) {
            setLoading(true);
            try {
                await deleteCategory(category.id).unwrap();
            } catch (error) {
                console.error('Failed to delete the category:', error);
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
                position: 'relative', // Make sure ListItem has position relative to allow absolute positioning of the CategoryButton
            }}
        >
            <Typography variant="body2" noWrap sx={{ flexGrow: 1 }}>
                {category.title}
            </Typography>
            <Box sx={{ display: 'flex', alignItems: 'center' }}>
                <CategoryButton category={category} />
                <IconButton
                    onClick={handleDelete}
                    disabled={loading || isDeleting}
                    sx={{ marginLeft: 1 }}
                >
                    <DeleteIcon />
                </IconButton>
            </Box>
        </ListItem>
    );
};

export default CategoryRow;
