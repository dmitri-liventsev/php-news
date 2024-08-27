import React, { useState, useEffect } from 'react';
import { Dialog, DialogActions, DialogContent, DialogTitle, TextField, Button, CircularProgress } from '@mui/material';
import { Category } from "./index";
import { useTranslation } from 'react-i18next';

interface CategoryFormModalProps {
    open: boolean;
    onClose: () => void;
    onSave: (category: Category) => void;
    title: string;
    category?: Category;
}

const CategoryFormModal: React.FC<CategoryFormModalProps> = ({ open, onClose, onSave, title, category }) => {
    const { t } = useTranslation();
    const [titleInput, setTitleInput] = useState(category ? category.title : '');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        if (category) {
            setTitleInput(category.title);
        }
    }, [category]);

    const validateTitle = (title: string) => {
        if (title.length < 3 || title.length > 250) {
            setError(t('category.titleTooShort'));
            return false;
        }
        setError(null);
        return true;
    };

    const handleSave = async () => {
        if (!validateTitle(titleInput)) {
            return;
        }

        setLoading(true);
        try {
            await onSave({ id: category?.id || 0, title: titleInput } as Category);
            onClose(); // Close modal after saving
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onClose={onClose}>
            <DialogTitle>{title}</DialogTitle>
            <DialogContent>
                <TextField
                    autoFocus
                    margin="dense"
                    label={t('category.titleLabel')}
                    fullWidth
                    variant="outlined"
                    value={titleInput}
                    onChange={(e) => setTitleInput(e.target.value)}
                    error={!!error}
                    helperText={error}
                />
            </DialogContent>
            <DialogActions>
                <Button
                    disabled={loading}
                    onClick={onClose}
                    color="primary"
                >
                    {t('category.cancel')}
                </Button>
                <Button
                    onClick={handleSave}
                    color="primary"
                    disabled={loading}
                >
                    {loading ? <CircularProgress size={24} /> : t('category.save')}
                </Button>
            </DialogActions>
        </Dialog>
    );
};

export default CategoryFormModal;
