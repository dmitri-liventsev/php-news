import React from 'react';
import { createRoot } from 'react-dom/client';
import { ThemeProvider } from '@mui/material/styles';
import theme from './theme';
import App from './App';
import {Provider} from "react-redux";
import {store} from "./store";

const rootElement = document.getElementById('root');
const root = createRoot(rootElement!);

root.render(<ThemeProvider theme={theme}>
        <Provider store={store}>
                <App />
        </Provider>
</ThemeProvider>)