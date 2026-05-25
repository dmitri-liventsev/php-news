import React from 'react';
import { Breadcrumbs as MuiBreadcrumbs, Link as MuiLink, Typography } from '@mui/material';
import { Link as RouterLink } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

export interface Crumb {
    label: string;
    to?: string;
}

interface Props {
    items: Crumb[];
}

const Breadcrumbs: React.FC<Props> = ({ items }) => {
    const { t } = useTranslation();

    const all: Crumb[] = [{ label: t('home'), to: '/' }, ...items];

    return (
        <MuiBreadcrumbs
            separator="::"
            sx={{
                position: 'sticky',
                top: 0,
                zIndex: (theme) => theme.zIndex.appBar,
                mx: -2,
                px: 2,
                py: 1.5,
                mb: 2,
                bgcolor: 'background.paper',
                borderBottom: '1px solid',
                borderColor: 'divider',
                boxShadow: '0 2px 4px rgba(0,0,0,0.04)',
            }}
        >
            {all.map((crumb, idx) => {
                const isLast = idx === all.length - 1;
                if (isLast || !crumb.to) {
                    return (
                        <Typography key={idx} color="text.primary">
                            {crumb.label}
                        </Typography>
                    );
                }
                return (
                    <MuiLink
                        key={idx}
                        component={RouterLink}
                        to={crumb.to}
                        underline="hover"
                        color="inherit"
                    >
                        {crumb.label}
                    </MuiLink>
                );
            })}
        </MuiBreadcrumbs>
    );
};

export default Breadcrumbs;