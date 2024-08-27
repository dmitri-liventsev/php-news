import React, { useState } from 'react';
import { Button } from '@mui/material';
import CategoryFormModal from './CategoryFormModal';
import { useCreateCategoryMutation, useUpdateCategoryMutation } from '../../features/api/apiSlice';
import {Category} from "./index";

interface CreateCategoryButtonProps {
    category?: Category;
}

const CategoryButton: React.FC<CreateCategoryButtonProps> = ({ category }) => {
    const [open, setOpen] = useState(false);
    const [createCategory] = useCreateCategoryMutation();
    const [updateCategory] = useUpdateCategoryMutation();

    const handleOpen = () => {
        setOpen(true);
    };

    const handleClose = () => {
        setOpen(false);
    };

    const handleSave = async (categoryData: Category) => {
        try {
            if (categoryData.id) {
                await updateCategory({ title: categoryData.title, id: categoryData.id }).unwrap();
            } else {
                await createCategory({ title: categoryData.title }).unwrap();
            }
            handleClose();
        } catch (error) {
            console.error('Failed to save category:', error);
        }
    };

    return (
        <>
            <Button
                variant="contained"
                color="primary"
                onClick={handleOpen}
            >
                {category ? 'Edit Category' : 'Create Category'}
            </Button>
            <CategoryFormModal
                open={open}
                onClose={handleClose}
                onSave={handleSave}
                title={category ? 'Edit Category' : 'Create Category'}
                category={category}
            />
        </>
    );
};

export default CategoryButton;
