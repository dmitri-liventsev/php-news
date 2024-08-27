import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { AppBar, Toolbar, Typography, Button, Container } from '@mui/material';
import { Outlet } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const Layout: React.FC = () => {
    const navigate = useNavigate();
    const { t } = useTranslation();

    const handleLogout = () => {
        localStorage.removeItem('token');
        navigate('/admin');
    };

    return (
        <div style={{width: '100%'}}>
            <AppBar position="static">
                <Toolbar>
                    <Typography variant="h6" style={{flexGrow: 1}}>
                        <Link to="/admin/articles" style={{color: 'inherit', textDecoration: 'none'}}>
                            {t('layout.articles')}
                        </Link>
                    </Typography>
                    <Typography variant="h6" style={{flexGrow: 1}}>
                        <Link to="/admin/categories" style={{color: 'inherit', textDecoration: 'none'}}>
                            {t('layout.categories')}
                        </Link>
                    </Typography>
                    <Button color="inherit" onClick={handleLogout}>{t('layout.logout')}</Button>
                </Toolbar>
            </AppBar>
            <Container>
                <Outlet/>
            </Container>
        </div>
    );
};

export default Layout;
