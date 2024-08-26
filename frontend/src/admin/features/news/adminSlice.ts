import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';

interface TokenState {
    token: string | null;
    error: string | null;
    loading: boolean;
}

interface AdminState {
    tokenState: TokenState;
}

const initialState: AdminState = {
    tokenState: {
        token: null,
        error: null,
        loading: false,
    },
};

export const login = createAsyncThunk<
    { token: string },
    { username: string; password: string },
    { rejectValue: string }
>(
    'auth/login',
    async ({ username, password }, thunkAPI) => {
        try {
            const response = await fetch('/admin/api/login_check', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Login failed');
            }

            const data = await response.json();
            return { token: data.token };
        } catch (error) {
            return thunkAPI.rejectWithValue((error as Error).message);
        }
    }
);

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(login.pending, (state) => {
                state.tokenState.loading = true;
                state.tokenState.error = null;
            })
            .addCase(login.fulfilled, (state, action) => {
                state.tokenState.loading = false;
                state.tokenState.token = action.payload.token;
                state.tokenState.error = null;
            })
            .addCase(login.rejected, (state, action) => {
                state.tokenState.loading = false;
                state.tokenState.error = action.payload || 'An error occurred';
            });
    },
});

export default authSlice.reducer;
