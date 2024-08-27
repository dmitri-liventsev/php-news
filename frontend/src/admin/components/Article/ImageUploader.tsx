import React, { useState } from 'react';
import {Box, IconButton, Button, Typography, CircularProgress} from '@mui/material';
import CloseIcon from '@mui/icons-material/Close';
import { styled } from '@mui/system';
import {Image} from "./index";

const ImagePreview = styled('img')({
    width: '100px',
    height: 'auto',
    borderRadius: '4px',
    marginBottom: '8px',
});

interface ImageUploaderProps {
    fileName: string | null;
    onChange: (image: Image) => void;
}

const ImageUploader: React.FC<ImageUploaderProps> = ({ fileName, onChange }) => {
    const [previewUrl, setPreviewUrl] = useState(fileName ? `/images/articles/${fileName}` : '');
    const [loading, setLoading] = useState(false);

    const handleImageChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            setLoading(true);
            setPreviewUrl(URL.createObjectURL(file));

            const formData = new FormData();
            formData.append('image', file);

            try {
                const token = localStorage.getItem('token') || '';

                const response = await fetch('/admin/api/articles/image/upload', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                const result = await response.json();
                if (response.ok) {
                    setPreviewUrl(`/images/articles/${result.file_name}`);
                    onChange(result);
                } else {
                    console.error('Upload failed:', result);
                }
            } catch (error) {
                console.error('Error uploading image:', error);
            } finally {
                setLoading(false);
            }
        }
    };

    const handleRemoveImage = () => {
        setPreviewUrl('');
        onChange({} as Image);
    };

    return (
        <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
            {previewUrl ? (
                <Box sx={{ position: 'relative', marginBottom: '8px' }}>
                    <ImagePreview src={previewUrl} alt="Image preview" />
                    <IconButton
                        onClick={handleRemoveImage}
                        sx={{
                            position: 'absolute',
                            top: 0,
                            right: 0,
                            backgroundColor: 'rgba(255, 255, 255, 0.7)',
                            borderRadius: '50%',
                        }}
                    >
                        <CloseIcon />
                    </IconButton>
                </Box>
            ) : (
                <Box sx={{ width: '100px', height: '100px', border: '1px dashed gray', display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
                    <Typography variant="body2">No Image</Typography>
                </Box>
            )}

            <Button
                variant="contained"
                component="label"
                sx={{ marginTop: '8px', position: 'relative' }}
                disabled={loading}  // Блокируем кнопку при загрузке
            >
                {loading ? (
                    <CircularProgress size={24} sx={{
                        color: 'white',
                        position: 'absolute',
                    }} />
                ) : (
                    fileName ? 'Change Image' : 'Upload Image'
                )}
                <input
                    type="file"
                    hidden
                    onChange={handleImageChange}
                />
            </Button>
        </Box>
    );
};

export default ImageUploader;
