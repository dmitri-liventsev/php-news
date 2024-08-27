import React, { useState } from 'react';
import { Button } from '@mui/material';
import CategoryFormModal from './CategoryFormModal';
import { useCreateCategoryMutation, useUpdateCategoryMutation } from '../../features/api/apiSlice';
import { Category } from "./index";
import { useTranslation } from 'react-i18next';

interface CreateCategoryButtonProps {
    category?: Category;
}

const CategoryButton: React.FC<CreateCategoryButtonProps> = ({ category }) => {
    const [open, setOpen] = useState(false);
    const [createCategory] = useCreateCategoryMutation();
    const [updateCategory] = useUpdateCategoryMutation();
    const { t } = useTranslation();

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
            console.error(t('category.saveFailed'), error);
        }
    };

    return (
        <>
            <Button
                variant="contained"
                color="primary"
                onClick={handleOpen}
            >
                {category ? t('category.editButton') : t('category.createButton')}
            </Button>
            <CategoryFormModal
                open={open}
                onClose={handleClose}
                onSave={handleSave}
                title={category ? t('category.modalTitleEdit') : t('category.modalTitleCreate')}
                category={category}
            />
        </>
    );
};

export default CategoryButton;
