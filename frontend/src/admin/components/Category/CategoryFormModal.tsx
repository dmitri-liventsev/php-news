import React, { useState, useEffect } from 'react';
import { Dialog, DialogActions, DialogContent, DialogTitle, TextField, Button, CircularProgress } from '@mui/material';
import {Category} from "./index";

interface CategoryFormModalProps {
    open: boolean;
    onClose: () => void;
    onSave: (category: Category) => void;
    title: string;
    category?: Category;
}

const CategoryFormModal: React.FC<CategoryFormModalProps> = ({ open, onClose, onSave, title, category }) => {
    const [titleInput, setTitleInput] = useState(category ? category.title : '');
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (category) {
            setTitleInput(category.title);
        }
    }, [category]);

    const handleSave = async () => {
        setLoading(true);
        try {
            await onSave({ id: category?.id || 0, title: titleInput } as Category);
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
                    label="Category Title"
                    fullWidth
                    variant="outlined"
                    value={titleInput}
                    onChange={(e) => setTitleInput(e.target.value)}
                />
            </DialogContent>
            <DialogActions>
                <Button
                    disabled={loading}
                    onClick={onClose} color="primary">
                    Cancel
                </Button>
                <Button
                    onClick={handleSave}
                    color="primary"
                    disabled={loading}
                >
                    {loading ? <CircularProgress size={24} /> : 'Save'}
                </Button>
            </DialogActions>
        </Dialog>
    );
};

export default CategoryFormModal;