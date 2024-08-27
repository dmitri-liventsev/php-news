// src/components/NotFound/NotFound.tsx
import React from 'react';
import { Box, Typography, Button } from '@mui/material';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const NotFound: React.FC = () => {
    const { t } = useTranslation();
    const navigate = useNavigate();

    return (
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100vh',
                textAlign: 'center',
            }}
        >
            <Typography variant="h4" gutterBottom>
                {t('notFound.title')}
            </Typography>
            <Typography variant="body1" paragraph>
                {t('notFound.message')}
            </Typography>
            <Button variant="contained" color="primary" onClick={() => navigate('/')}>
                {t('notFound.button')}
            </Button>
        </Box>
    );
};

export default NotFound;