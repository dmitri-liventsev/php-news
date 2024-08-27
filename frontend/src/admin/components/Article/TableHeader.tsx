import React from 'react';
import { Box, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';

interface Props {}

const TableHeader: React.FC<Props> = () => {
    const { t } = useTranslation();

    return (
        <Box
            sx={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                borderBottom: '1px solid #ddd',
                padding: '8px',
            }}
        >
            <Box
                sx={{
                    display: 'flex',
                    flexDirection: 'row',
                    alignItems: 'center',
                    width: '100%',
                }}
            >
                <Box sx={{ width: '200px', overflow: 'hidden' }}>
                    <Typography
                        variant="h6"
                        noWrap
                        sx={{ fontWeight: 'bold', fontSize: '1.25rem' }}
                    >
                        {t('tableHeader.title')}
                    </Typography>
                </Box>
                <Box sx={{ width: '500px', overflow: 'hidden' }}>
                    <Typography
                        variant="h6"
                        noWrap
                        sx={{ fontWeight: 'bold', fontSize: '1.25rem' }}
                    >
                        {t('tableHeader.shortDescription')}
                    </Typography>
                </Box>
            </Box>
            <Box>
                {/* Add additional content if needed */}
            </Box>
        </Box>
    );
};

export default TableHeader;
