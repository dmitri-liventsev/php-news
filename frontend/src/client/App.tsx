import React from 'react';
import './styles/App.scss';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import TopNews from './components/TopNews/TopNews';
import Category from './components/Category/Category';
import Article from './components/Article/Article';
import {Box} from "@mui/material";
import NotFound from "./components/Util/NotFound";

const App: React.FC = () => {
    return (
        <Router>
            <Box sx={{
                display: 'flex',
                justifyContent: 'center',
                width: '100%',
                mb: 2
            }}>
                <Routes>
                    <Route path="/" element={<TopNews />} />
                    <Route path="/category/:categoryId" element={<Category />} />
                    <Route path="/article/:articleId" element={<Article />} />
                    <Route path="*" element={<NotFound />} />
                </Routes>
            </Box>
        </Router>
    );
};

export default App;