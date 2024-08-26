import React from 'react';
import {Box, Card, CardContent, CardMedia, IconButton, Typography} from '@mui/material';
import { Article } from './index';
import {Link} from "react-router-dom";
import VisibilityIcon from '@mui/icons-material/Visibility';
import DeleteIcon from '@mui/icons-material/Delete';

interface Props {

}

const TableHeader: React.FC<Props> = () => {
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
                        sx={{ fontWeight: 'bold', fontSize: '1.25rem' }} // Customize font weight and size here
                    >
                        Title
                    </Typography>
                </Box>
                <Box sx={{ width: '500px', overflow: 'hidden' }}>
                    <Typography
                        variant="h6"
                        noWrap
                        sx={{ fontWeight: 'bold', fontSize: '1.25rem' }} // Customize font weight and size here
                    >
                        Short description
                    </Typography>
                </Box>
            </Box>
            <Box>
            </Box>
        </Box>
    );
};

export default TableHeader;
