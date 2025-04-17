import { createTheme, alpha } from '@mui/material/styles';

// Color tokens
const tokens = {
    primary: {
        50: '#f0f4fb',
        100: '#d3e0f2',
        400: '#2564bf',  // Primary color
        600: '#124ea4',  // Hover
        800: '#0a1528',  // Focus/Active
    },
    neutral: {
        50: '#ffffff',   // Background
        100: '#e7eaf2',  // Border
        200: '#94a0b8',  // Placeholder
        300: '#778ca6',  // Tertiary border
        400: '#617495',  // Tertiary text
        500: '#36486a',  // Secondary text
        600: '#0f2141',  // Primary text
        800: '#0a1528',  // Heading text
    },
    secondary: {
        red: {
            50: '#fdf4f6',
            400: '#e96a81',
            600: '#c51d3b',
            800: '#b40c2a',
        },
        green: {
            50: '#f3f9f6',
            400: '#78c09b',
            600: '#119c55',
            800: '#03833c',
        },
        blue: {
            50: '#f2f9fe',
            400: '#91caf9',
            600: '#2992e8',
            800: '#1174c6',
        },
        orange: {
            50: '#fff8f5',
            400: '#fcba98',
            600: '#df6e36',
            800: '#c24f16',
        },
    },
};

export const theme = createTheme({
    palette: {
        mode: 'light',
        primary: {
            main: tokens.primary[600],
            light: tokens.primary[400],
            dark: tokens.primary[800],
            contrastText: tokens.neutral[50],
        },
        secondary: {
            main: tokens.secondary.green[600],
            light: tokens.secondary.green[400],
            dark: tokens.secondary.green[800],
            contrastText: tokens.neutral[50],
        },
        error: {
            main: tokens.secondary.red[600],
            light: tokens.secondary.red[400],
            dark: tokens.secondary.red[800],
            contrastText: tokens.neutral[50],
        },
        warning: {
            main: tokens.secondary.orange[600],
            light: tokens.secondary.orange[400],
            dark: tokens.secondary.orange[800],
            contrastText: tokens.neutral[800],
        },
        info: {
            main: tokens.secondary.blue[600],
            light: tokens.secondary.blue[400],
            dark: tokens.secondary.blue[800],
            contrastText: tokens.neutral[50],
        },
        success: {
            main: tokens.secondary.green[600],
            light: tokens.secondary.green[400],
            dark: tokens.secondary.green[800],
            contrastText: tokens.neutral[50],
        },
        grey: {
            50: tokens.neutral[50],
            100: tokens.neutral[100],
            200: tokens.neutral[200],
            300: tokens.neutral[300],
            400: tokens.neutral[400],
            500: tokens.neutral[500],
            600: tokens.neutral[600],
            700: tokens.neutral[600],
            800: tokens.neutral[800],
            900: tokens.neutral[800],
        },
        background: {
            default: tokens.neutral[50],
            paper: tokens.neutral[50],
        },
        text: {
            primary: tokens.neutral[800],
            secondary: tokens.neutral[600],
            disabled: tokens.neutral[400],
        },
        divider: tokens.neutral[200],
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
                    borderRadius: 3,
                    padding: '8px 16px',
                },
                contained: {
                    boxShadow: 'none',
                    '&:hover': {
                        boxShadow: `0 2px 4px ${alpha(tokens.neutral[800], 0.08)}`,
                    },
                },
                outlined: {
                    borderWidth: 1,
                    '&:hover': {
                        borderWidth: 1,
                    },
                },
                startIcon: {
                    marginRight: 8,
                },
            },
        },
        MuiPaper: {
            styleOverrides: {
                root: {
                    backgroundImage: 'none',
                },
                elevation1: {
                    boxShadow: `0 1px 3px ${alpha(tokens.neutral[800], 0.1)}, 0 1px 2px ${alpha(tokens.neutral[800], 0.06)}`,
                },
            },
        },
        MuiCard: {
            styleOverrides: {
                root: {
                    backgroundImage: 'none',
                    border: '1px solid',
                    borderColor: tokens.neutral[100],
                    borderRadius: 4,
                    padding: '24px',
                },
            },
        },
        MuiTableCell: {
            styleOverrides: {
                root: {
                    borderBottom: `1px solid ${tokens.neutral[100]}`,
                    padding: '12px 16px',
                },
                head: {
                    fontWeight: 600,
                    backgroundColor: tokens.neutral[50],
                    color: tokens.neutral[600],
                },
            },
        },
        MuiAlert: {
            styleOverrides: {
                root: {
                    borderRadius: 4,
                    padding: '16px 24px',
                    borderWidth: 1,
                    borderStyle: 'solid',
                },
                standardInfo: {
                    backgroundColor: tokens.secondary.blue[50],
                    borderColor: tokens.secondary.blue[400],
                    color: tokens.secondary.blue[600],
                    '& .MuiAlert-icon': {
                        color: tokens.secondary.blue[600],
                    },
                },
                standardSuccess: {
                    backgroundColor: tokens.secondary.green[50],
                    borderColor: tokens.secondary.green[400],
                    color: tokens.secondary.green[600],
                    '& .MuiAlert-icon': {
                        color: tokens.secondary.green[600],
                    },
                },
                standardWarning: {
                    backgroundColor: tokens.secondary.orange[50],
                    borderColor: tokens.secondary.orange[400],
                    color: tokens.secondary.orange[600],
                    '& .MuiAlert-icon': {
                        color: tokens.secondary.orange[600],
                    },
                },
                standardError: {
                    backgroundColor: tokens.secondary.red[50],
                    borderColor: tokens.secondary.red[400],
                    color: tokens.secondary.red[600],
                    '& .MuiAlert-icon': {
                        color: tokens.secondary.red[600],
                    },
                },
            },
        },
        MuiTextField: {
            styleOverrides: {
                root: {
                    '& .MuiOutlinedInput-root': {
                        borderRadius: 4,
                        '& fieldset': {
                            borderColor: tokens.neutral[100],
                            borderWidth: 1,
                        },
                        '&:hover fieldset': {
                            borderColor: tokens.primary[400],
                        },
                        '&.Mui-focused fieldset': {
                            borderColor: tokens.primary[600],
                        },
                        '&.Mui-disabled': {
                            backgroundColor: tokens.neutral[50],
                            borderColor: tokens.neutral[100],
                            color: tokens.neutral[300],
                        },
                    },
                    '& .MuiInputBase-input': {
                        padding: '8px 12px',
                        fontSize: '14px',
                        color: tokens.neutral[800],
                        '&::placeholder': {
                            color: tokens.neutral[200],
                            opacity: 1,
                        },
                    },
                },
            },
        },
        MuiAutocomplete: {
            styleOverrides: {
                root: {
                    '& .MuiOutlinedInput-root': {
                        borderRadius: 4,
                        '& fieldset': {
                            borderColor: tokens.neutral[100],
                            borderWidth: 1,
                        },
                        '&:hover fieldset': {
                            borderColor: tokens.primary[400],
                        },
                        '&.Mui-focused fieldset': {
                            borderColor: tokens.primary[600],
                        },
                        '&.Mui-disabled': {
                            backgroundColor: tokens.neutral[50],
                            borderColor: tokens.neutral[100],
                            color: tokens.neutral[300],
                        },
                        '& .MuiAutocomplete-input': {
                            padding: '8px 12px !important',
                            fontSize: '14px',
                            color: tokens.neutral[800],
                        },
                    },
                },
                paper: {
                    borderRadius: 4,
                    marginTop: 4,
                    boxShadow: `0 4px 6px -1px ${alpha(tokens.neutral[800], 0.1)}, 0 2px 4px -1px ${alpha(tokens.neutral[800], 0.06)}`,
                },
                listbox: {
                    padding: '4px 0',
                    '& .MuiAutocomplete-option': {
                        padding: '8px 12px',
                        fontSize: '14px',
                    },
                },
            },
        },
        MuiSelect: {
            styleOverrides: {
                select: {
                    padding: '8px 12px',
                    fontSize: '14px',
                    color: tokens.neutral[800],
                    '&:focus': {
                        backgroundColor: 'transparent',
                    },
                },
            },
        },
    },
    shape: {
        borderRadius: 8,
    },
});

export default theme;
