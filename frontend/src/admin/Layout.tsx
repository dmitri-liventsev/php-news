import React from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { AppBar, Toolbar, Typography, Button, Container } from '@mui/material';
import { Outlet } from 'react-router-dom';

const Layout: React.FC = () => {
    const navigate = useNavigate();

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
                            Articles
                        </Link>
                    </Typography>
                    <Typography variant="h6" style={{flexGrow: 1}}>
                        <Link to="/admin/categories" style={{color: 'inherit', textDecoration: 'none'}}>
                            Categories
                        </Link>
                    </Typography>
                    <Button color="inherit" onClick={handleLogout}>LogOut</Button>
                </Toolbar>
            </AppBar>
            <Container>
                <Outlet/>
            </Container>
        </div>
    );
};

export default Layout;
