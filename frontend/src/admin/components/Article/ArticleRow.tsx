import React, { useState } from 'react';
import { Box, Typography, IconButton } from '@mui/material';
import { Article } from './index';
import { Link } from 'react-router-dom';
import VisibilityIcon from '@mui/icons-material/Visibility';
import DeleteIcon from '@mui/icons-material/Delete';
import { useDeleteArticleMutation } from '../../features/api/apiSlice';

interface Props {
    article: Article;
}

const ArticleRow: React.FC<Props> = ({ article }) => {
    const [deleteArticle, { isLoading: isDeleting }] = useDeleteArticleMutation();
    const [loading, setLoading] = useState(false);

    const handleDelete = async () => {
        if (window.confirm('Are you sure you want to delete this article?')) {
            setLoading(true);
            try {
                await deleteArticle(article.id).unwrap();
            } catch (error) {
                console.error('Failed to delete the article:', error);
            } finally {
                setLoading(false);
            }
        }
    };

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
                    <Link to={`/admin/article/${article.id}`}>
                        <Typography variant="body2" noWrap>
                            {article.title}
                        </Typography>
                    </Link>
                </Box>
                <Box sx={{ width: '500px', overflow: 'hidden' }}>
                    <Typography variant="body2" noWrap>
                        {article.shortDescription}
                    </Typography>
                </Box>
            </Box>
            <Box>
                <Link to={`/article/${article.id}`}>
                    <IconButton>
                        <VisibilityIcon />
                    </IconButton>
                </Link>

                <IconButton onClick={handleDelete} disabled={loading || isDeleting}>
                    <DeleteIcon />
                </IconButton>
            </Box>
        </Box>
    );
};

export default ArticleRow;
