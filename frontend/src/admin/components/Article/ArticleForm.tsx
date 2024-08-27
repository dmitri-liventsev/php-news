import React, { useState, useEffect } from 'react';
import {
    Box,
    TextField,
    Button,
    Typography,
    FormControl,
    MenuItem,
    Select,
    InputLabel,
    Checkbox as MuiCheckbox,
    ListItemText,
    CircularProgress,
    FormControlLabel,
} from '@mui/material';
import { useNavigate, useParams } from 'react-router-dom';
import { useCreateArticleMutation, useUpdateArticleMutation, useFetchArticleQuery, useFetchCategoriesQuery } from '../../features/api/apiSlice';
import ImageUploader from './ImageUploader';
import { Article, Image } from './index';
import Loading from '../Util/Loading';
import { useTranslation } from 'react-i18next';

const ArticleForm: React.FC = () => {
    const { t } = useTranslation();
    const navigate = useNavigate();
    const { article_id } = useParams<{ article_id?: string }>();
    const [createArticle, { isLoading: isCreating }] = useCreateArticleMutation();
    const [updateArticle, { isLoading: isUpdating }] = useUpdateArticleMutation();
    const { data: article, isLoading: isFetchingArticle } = useFetchArticleQuery(article_id ? parseInt(article_id) : 0, {
        skip: !article_id,
    });
    const { data: categoriesData, isLoading: isFetchingCategories } = useFetchCategoriesQuery();

    const [title, setTitle] = useState('');
    const [shortDescription, setShortDescription] = useState('');
    const [content, setContent] = useState('');
    const [imageId, setImageId] = useState<number | null>(null);
    const [imageFileName, setImageFileName] = useState<string | null>(null);
    const [selectedCategories, setSelectedCategories] = useState<number[]>([]);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [errors, setErrors] = useState<{ [key: string]: string }>({});

    useEffect(() => {
        if (article) {
            setTitle(article.title);
            setShortDescription(article.shortDescription);
            setContent(article.content);
            setImageId(article.image.id);
            setImageFileName(article.image.fileName);
            setSelectedCategories(article.categories.map(category => category.id));
        }
    }, [article]);

    const validateForm = () => {
        const newErrors: { [key: string]: string } = {};
        if (title.trim().length < 3 || title.trim().length > 250) {
            newErrors.title = title.trim().length < 3
                ? t('formErrors.titleTooShort')
                : t('formErrors.titleTooLong');
        }
        if (shortDescription.trim().length < 3 || shortDescription.trim().length > 250) {
            newErrors.shortDescription = shortDescription.trim().length < 3
                ? t('formErrors.shortDescriptionTooShort')
                : t('formErrors.shortDescriptionTooLong');
        }
        if (content.trim().length < 3) {
            newErrors.content = t('formErrors.contentTooShort');
        }
        if (selectedCategories.length === 0) {
            newErrors.categories = t('formErrors.categoriesEmpty');
        }
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!validateForm()) return;

        setIsSubmitting(true);
        const articleData = {
            title,
            shortDescription,
            content,
            imageID: imageId ?? null,
            categories: selectedCategories,
        };

        try {
            if (article_id) {
                await updateArticle({ articleId: parseInt(article_id), article: articleData }).unwrap();
            } else {
                await createArticle(articleData).unwrap();
            }
            navigate('/admin/articles');
        } catch (error) {
            console.error('Failed to save the article:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleImageChange = (image: Image) => {
        setImageId(image.id === 0 ? null : image.id);
        setImageFileName(image.id === 0 ? null : image.fileName);
    };

    const handleCategoryChange = (event: any) => {
        setSelectedCategories(event.target.value as number[]);
    };

    if (isFetchingArticle || isFetchingCategories) return <Loading />;

    return (
        <Box sx={{ padding: 2 }}>
            <Typography variant="h4" gutterBottom>
                {article_id ? t('form.editArticle') : t('form.createArticle')}
            </Typography>
            <form onSubmit={handleSubmit}>
                <TextField
                    label={t('form.title')}
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    required
                    error={!!errors.title}
                    helperText={errors.title}
                />
                <TextField
                    label={t('form.shortDescription')}
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    value={shortDescription}
                    onChange={(e) => setShortDescription(e.target.value)}
                    required
                    error={!!errors.shortDescription}
                    helperText={errors.shortDescription}
                />
                <TextField
                    label={t('form.content')}
                    variant="outlined"
                    fullWidth
                    margin="normal"
                    multiline
                    rows={4}
                    value={content}
                    onChange={(e) => setContent(e.target.value)}
                    required
                    error={!!errors.content}
                    helperText={errors.content}
                />
                <ImageUploader
                    fileName={imageFileName}
                    onChange={handleImageChange}
                />
                <FormControl fullWidth margin="normal" error={!!errors.categories}>
                    <InputLabel id="categories-select-label">{t('form.categories')}</InputLabel>
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
                    {!!errors.categories && <Typography color="error">{errors.categories}</Typography>}
                </FormControl>
                <Button
                    variant="contained"
                    color="primary"
                    type="submit"
                    sx={{ marginTop: 2, position: 'relative' }}
                    disabled={isSubmitting}
                >
                    {isSubmitting ? (
                        <CircularProgress size={24} />
                    ) : (
                        article_id ? t('form.updateArticle') : t('form.createArticle')
                    )}
                </Button>
            </form>
        </Box>
    );
};

export default ArticleForm;
