import React from 'react';
import './styles/App.scss';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import TopNews from './components/TopNews/TopNews';
import Category from './components/Category/Category';
import Article from './components/Article/Article';
import {Box} from "@mui/material";

const App = () => {
    return (
        <Router>
            <Box sx={{
                display: 'flex',
                justifyContent: 'center',
                width: '100%',
                mb: 2
            }}>
                <Routes>
                    <Route path="/" Component={TopNews} />
                    <Route path="/category/:categoryId" Component={Category} />
                    <Route path="/article/:articleId" Component={Article} />
                </Routes>
            </Box>
        </Router>
    );
};

export default App;