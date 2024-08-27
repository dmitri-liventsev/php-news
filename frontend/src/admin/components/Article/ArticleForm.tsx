import React, { useState, useEffect } from 'react';
import {
    Box,
    TextField,
    Button,
    Typography,
    FormControlLabel,
    Checkbox,
    MenuItem,
    Select,
    InputLabel,
    Checkbox as MuiCheckbox,
    ListItemText,
    FormControl, SelectChangeEvent
} from '@mui/material';
import { useNavigate, useParams } from 'react-router-dom';
import { useCreateArticleMutation, useUpdateArticleMutation, useFetchArticleQuery, useFetchCategoriesQuery } from '../../features/api/apiSlice';
import ImageUploader from './ImageUploader';
import {Article, Image} from './index';
import Loading from "../Util/Loading";

const ArticleForm: React.FC = () => {
    const navigate = useNavigate();
    const { articleId } = useParams<{ articleId?: string }>();
    const [createArticle] = useCreateArticleMutation();
    const [updateArticle] = useUpdateArticleMutation();
    const { data: article, isLoading: isFetchingArticle } = useFetchArticleQuery(articleId ? parseInt(articleId) : 0, {
        skip: !articleId,
    });
    const { data: categoriesData, isLoading: isFetchingCategories } = useFetchCategoriesQuery();

    const [title, setTitle] = useState('');
    const [shortDescription, setShortDescription] = useState('');
    const [content, setContent] = useState('');
    const [imageId, setImageId] = useState<number | null>(null);
    const [imageFileName, setImageFileName] = useState<string | null>(null);
    const [selectedCategories, setSelectedCategories] = useState<number[]>([]);

    useEffect(() => {
        if (article) {
            setTitle(article.title);
            setShortDescription(article.shortDescription);
            setContent(article.content);
            setImageId(article.image.id);
            setSelectedCategories(article.categories.map(category => category.id));
        }
    }, [article]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        const articleData = {
            title,
            shortDescription,
            content,
            imageId: imageId ?? null,
            categories: selectedCategories,
        };

        try {
            if (articleId) {
                await updateArticle({ articleId: parseInt(articleId), article: articleData }).unwrap();
            } else {
                await createArticle(articleData).unwrap();
            }
            navigate('/admin/articles');
        } catch (error) {
            console.error('Failed to save the article:', error);
        }
    };

    const handleImageChange = (image: Image) => {
        setImageId(image.id === 0 ? null : image.id);
        setImageFileName(image.id === 0 ? null : image.fileName);
    };

    const handleCategoryChange = (event: SelectChangeEvent<number[]>) => {
        setSelectedCategories(event.target.value as number[]);
    };

    if (isFetchingArticle || isFetchingCategories) return <Loading />;

    return (
        <Box sx={{ padding: 2 }}>
            <Typography variant="h4" gutterBottom>
                {articleId ? 'Edit Article' : 'Create Article'}
            </Typography>
            <form onSubmit={handleSubmit}>
                <TextField
                    label="Title"
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    required
                />
                <TextField
                    label="Short Description"
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    value={shortDescription}
                    onChange={(e) => setShortDescription(e.target.value)}
                    required
                />
                <TextField
                    label="Content"
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    multiline
                    rows={4}
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    required
                />
                <ImageUploader
                    fileName={imageFileName}
                    onChange={handleImageChange}
                />
                <FormControl fullWidth margin="normal">
                    <InputLabel id="categories-select-label">Categories</InputLabel>
                    <Select
                        labelId="categories-select-label"
                        multiple
                        value={selectedCategories}
                        onChange={handleCategoryChange}
                        renderValue={(selected) => (
                            <Box sx={{ display: 'flex', flexWrap: 'wrap', gap: 0.5 }}>
                                {(selected as number[]).map((id) => {
                                    const category = categoriesData?.find(category => category.id === id);
                                    return (
                                        <Box key={id} sx={{ display: 'flex', alignItems: 'center' }}>
                                            {category?.title}
                                        </Box>
                                    );
                                })}
                            </Box>
                        )}
                        MenuProps={{
                            PaperProps: {
                                sx: {
                                    maxHeight: 200,
                                },
                            },
                        }}
                    >
                        {categoriesData?.map((category) => (
                            <MenuItem key={category.id} value={category.id}>
                                <MuiCheckbox checked={selectedCategories.indexOf(category.id) > -1} />
                                <ListItemText primary={category.title} />
                            </MenuItem>
                        ))}
                    </Select>
                </FormControl>
                <Button variant="contained" color="primary" type="submit" sx={{ marginTop: 2 }}>
                    {articleId ? 'Update Article' : 'Create Article'}
                </Button>
            </form>
        </Box>
    );
};

export default ArticleForm;
