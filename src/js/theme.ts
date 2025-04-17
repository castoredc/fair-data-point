import { createTheme } from '@mui/material/styles';

export const theme = createTheme({
    palette: {
        primary: {
            main: '#2868c4',
            light: '#5fafff',
            dark: '#3070ce',
        },
        secondary: {
            main: '#2cd06c',
            light: '#a5ffc6',
            dark: '#28bc5f',
        },
        error: {
            main: '#dc143c',
            light: '#ff8080',
            dark: '#bb022e',
        },
        warning: {
            main: '#ffa333',
            light: '#ffc98f',
            dark: '#e8851b',
        },
        info: {
            main: '#47d6ff',
            light: '#caf8ff',
            dark: '#2ccdff',
        },
        success: {
            main: '#2cd06c',
            light: '#a5ffc6',
            dark: '#28bc5f',
        },
        grey: {
            50: '#fafbfb',
            100: '#eceef0',
            200: '#c3c7cf',
            300: '#b5bac4',
            400: '#a8afba',
            500: '#9aa2af',
            600: '#8c95a4',
            700: '#7d8798',
            800: '#6f7a8d',
            900: '#616d82',
        },
        background: {
            default: '#ffffff',
            paper: '#ffffff',
        },
        text: {
            primary: '#293956',
            secondary: '#556178',
            disabled: '#9aa2af',
        },
    },
    typography: {
        fontFamily: [
            '-apple-system',
            'BlinkMacSystemFont',
            '"Segoe UI"',
            'Roboto',
            '"Helvetica Neue"',
            'Arial',
            'sans-serif',
        ].join(','),
    },
    components: {
        MuiButton: {
            styleOverrides: {
                root: {
                    textTransform: 'none',
                },
                startIcon: {
                    marginRight: 5,
                },
            },
        },
    },
});
