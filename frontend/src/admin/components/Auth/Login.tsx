import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import {AppDispatch} from "../../../client/store";
import {login} from "../../features/news/adminSlice";
import {RootState} from "../../store";

const Login: React.FC = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState<string | null>(null);
    const navigate = useNavigate();
    const { tokenState } = useSelector((state: RootState) => state.admin);

    const dispatch = useDispatch<AppDispatch>();

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            const resultAction = await dispatch(login({ username, password })).unwrap();
            const { token } = resultAction;
            localStorage.setItem('token', token);
            navigate('/admin/articles');
        } catch (err) {
            //TODO handler error
            console.error('Login failed:', err);
        }
    };
    return (
        <div>
            <h2>Login</h2>
            {error && <p>{error}</p>}
            <form onSubmit={handleLogin}>
                <input
                    type="text"
                    placeholder="Email"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                />
                <br/>
                <input
                    type="password"
                    placeholder="Password"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                />
                <br/>
                <button type="submit" disabled={tokenState.loading}>
                    {tokenState.loading ? 'Logging in...' : 'Login'}
                </button>
            </form>
        </div>
    );
};

export default Login;
