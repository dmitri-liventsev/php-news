import * as React from 'react';
import { createTheme } from '@mui/material/styles';
import { red } from '@mui/material/colors';

// A custom theme for this app
const theme = createTheme({
    components: {
        MuiLink: {
            defaultProps: {
                className: 'react_page',
            }
        },
    },
    palette: {
        primary: {
            main: '#556cd6',
        },
        secondary: {
            main: '#19857b',
        },
        error: {
            main: red.A400,
        },
    },
    typography: {
        fontFamily: [
            'msans',
            'sans-serif'
        ].join(','),
        fontSize: 12,
    }
});

export default theme;
