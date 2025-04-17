import { createTheme, alpha } from '@mui/material/styles';
import { blue, grey, red } from '@mui/material/colors';

export const theme = createTheme({
    palette: {
        primary: {
            main: blue[700],
            light: blue[500],
            dark: blue[900],
        },
        secondary: {
            main: '#2cd06c',
            light: '#a5ffc6',
            dark: '#28bc5f',
        },
        error: {
            main: red[700],
            light: red[500],
            dark: red[900],
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
            50: grey[50],
            100: grey[100],
            200: grey[200],
            300: grey[300],
            400: grey[400],
            500: grey[500],
            600: grey[600],
            700: grey[700],
            800: grey[800],
            900: grey[900],
        },
        background: {
            default: grey[50],
            paper: '#fff',
        },
        text: {
            primary: grey[900],
            secondary: grey[600],
            disabled: grey[400],
        },
        divider: grey[200],
    },
    typography: {
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif',
        h1: {
            fontSize: '2.5rem',
            fontWeight: 600,
            lineHeight: 1.2,
        },
        h2: {
            fontSize: '2rem',
            fontWeight: 600,
            lineHeight: 1.3,
        },
        h3: {
            fontSize: '1.75rem',
            fontWeight: 600,
            lineHeight: 1.3,
        },
        h4: {
            fontSize: '1.5rem',
            fontWeight: 600,
            lineHeight: 1.4,
        },
        h5: {
            fontSize: '1.25rem',
            fontWeight: 600,
            lineHeight: 1.4,
        },
        h6: {
            fontSize: '1rem',
            fontWeight: 600,
            lineHeight: 1.5,
        },
        body1: {
            fontSize: '1rem',
            lineHeight: 1.5,
        },
        body2: {
            fontSize: '0.875rem',
            lineHeight: 1.57,
        },
        button: {
            textTransform: 'none',
            fontWeight: 500,
        },
    },
    components: {
        MuiButton: {
            styleOverrides: {
                root: {
                    textTransform: 'none',
                    fontWeight: 500,
                },
                startIcon: {
                    marginRight: 5,
                },
            },
        },
        MuiPaper: {
            styleOverrides: {
                root: {
                    backgroundImage: 'none',
                },
            },
        },
        MuiCard: {
            styleOverrides: {
                root: {
                    backgroundImage: 'none',
                    border: '1px solid',
                    borderColor: grey[200],
                },
            },
        },
        MuiTableCell: {
            styleOverrides: {
                root: {
                    borderBottom: `1px solid ${grey[200]}`,
                },
                head: {
                    fontWeight: 600,
                    backgroundColor: grey[50],
                },
            },
        },
        MuiAlert: {
            styleOverrides: {
                root: {
                    borderRadius: 8,
                },
                standardInfo: {
                    backgroundColor: alpha(blue[700], 0.1),
                    color: blue[900],
                },
                standardError: {
                    backgroundColor: alpha(red[700], 0.1),
                    color: red[900],
                },
            },
        },
    },
    shape: {
        borderRadius: 8,
    },
});

export default theme;
