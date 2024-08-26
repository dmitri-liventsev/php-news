export const handleError = async (response: Response): Promise<void> => {
    if (!response.ok) {
        if (response.status === 401) {
            localStorage.removeItem('token');
        }
    }
};