import React from 'react';
import './styles/App.scss';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import CategoryList from './components/Category/CategoryList';
import ArticleList from './components/Article/ArticleList';
import Article from './components/Article/ArticleForm';
import CommentList from './components/Comment/CommentList';
import Login from "./components/Auth/Login";
import PrivateRoute from "./PrivateRoute";
import Layout from "./Layout";
import {Box} from "@mui/material";
import ArticleForm from "./components/Article/ArticleForm";

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
                    <Route path="/admin" element={<Login />} />
                    <Route element={<PrivateRoute />}>
                        <Route path="/admin" element={<Layout />}>
                            <Route path="articles" element={<ArticleList />} />
                            <Route path="articles/create" element={<ArticleForm />} />
                            <Route path="article/:article_id" element={<Article />} />
                            <Route path="article/:article_id/comments" element={<CommentList />} />
                            <Route path="categories" element={<CategoryList />} />
                        </Route>
                    </Route>
                </Routes>
            </Box>
        </Router>
    );
};

export default App;