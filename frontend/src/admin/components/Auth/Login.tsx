import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useLoginMutation } from '../../features/api/apiSlice';
import { CircularProgress, Box, TextField, Button, Typography } from '@mui/material';
import { useTranslation } from 'react-i18next';

const Login: React.FC = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);
    const navigate = useNavigate();
    const { t } = useTranslation();

    const [login, { isLoading }] = useLoginMutation();

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            const result = await login({ username, password }).unwrap();
            const { token } = result;
            localStorage.setItem('token', token);
            navigate('/admin/articles');
        } catch (err) {
            setError(t('login.loginFailed'));
        }
    };

    return (
        <Box sx={{ display: 'flex', flexDirection: 'column', alignItems: 'center', padding: 2 }}>
            <Typography variant="h4" gutterBottom>
                {t('login.heading')}
            </Typography>
            {error && <Typography color="error">{error}</Typography>}
            <form onSubmit={handleLogin} style={{ width: '100%', maxWidth: 400 }}>
                <TextField
                    label={t('login.emailPlaceholder')}
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                />
                <TextField
                    label={t('login.passwordPlaceholder')}
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    type="password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                />
                <Button
                    type="submit"
                    variant="contained"
                    color="primary"
                    fullWidth
                    sx={{ mt: 2 }}
                    disabled={isLoading}
                >
                    {isLoading ? <CircularProgress size={24} /> : t('login.loginButton')}
                </Button>
            </form>
        </Box>
    );
};

export default Login;
