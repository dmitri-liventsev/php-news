import React from 'react';
import { Box, Typography, Button } from '@mui/material';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const NotFound: React.FC = () => {
    const { t } = useTranslation();

    return (
        <Box
            sx={{
                display: 'flex',
                flexDirection: 'column',
                alignItems: 'center',
                justifyContent: 'center',
                height: '100vh',
                textAlign: 'center',
                padding: 2,
            }}
        >
            <Typography variant="h2" color="textPrimary">
                404
            </Typography>
            <Typography variant="h4" color="textSecondary">
                {t('notFound.title')}
            </Typography>
            <Typography variant="body1" color="textSecondary" sx={{ mb: 2 }}>
                {t('notFound.message')}
            </Typography>
            <Button
                component={Link}
                to="/admin/articles"
                variant="contained"
                color="primary"
            >
                {t('notFound.home')}
            </Button>
        </Box>
    );
};

export default NotFound;
