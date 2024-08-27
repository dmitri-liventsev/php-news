import React from 'react';
import { Box, CircularProgress } from '@mui/material';

const Loading: React.FC = () => {
    return (
        <Box
            sx={{
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                marginTop: '50px',
            }}
        >
            <CircularProgress />
        </Box>
    );
};

export default Loading;