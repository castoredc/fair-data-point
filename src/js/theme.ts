import { createTheme, alpha } from '@mui/material/styles';
import { svgIconClasses } from '@mui/material/SvgIcon';
import { typographyClasses } from '@mui/material/Typography';
import { buttonBaseClasses } from '@mui/material/ButtonBase';
import { iconButtonClasses } from '@mui/material/IconButton';
import { listItemIconClasses } from '@mui/material/ListItemIcon';
import { gridClasses } from '@mui/x-data-grid';
import { checkboxClasses } from '@mui/material/Checkbox';
import { tablePaginationClasses } from '@mui/material/TablePagination';
import { menuItemClasses } from '@mui/material/MenuItem';
import { listClasses } from '@mui/material/List';
import { paperClasses } from '@mui/material/Paper';
import { Components } from '@mui/material/styles';

// Augment the theme components to include MUI X DataGrid
declare module '@mui/material/styles' {
  interface Components<Theme = unknown> {
    MuiDataGrid?: {
      defaultProps?: Record<string, any>;
      styleOverrides?: {
        root?: Record<string, any>;
        [key: string]: any;
      };
    };
  }
}

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
        35: '#ebecef',
        50: '#f4f5f8',   // Background
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
    white: '#ffffff',
    black: {
        100: '#cfd6e2',
    }
};

export const theme = createTheme({
    palette: {
        mode: 'light',
        primary: {
            main: tokens.primary[600],
            light: tokens.primary[400],
            dark: tokens.primary[800],
            contrastText: tokens.white,
        },
        secondary: {
            main: tokens.secondary.green[600],
            light: tokens.secondary.green[400],
            dark: tokens.secondary.green[800],
            contrastText: tokens.white,
        },
        error: {
            main: tokens.secondary.red[600],
            light: tokens.secondary.red[400],
            dark: tokens.secondary.red[800],
            contrastText: tokens.white,
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
            contrastText: tokens.white,
        },
        success: {
            main: tokens.secondary.green[600],
            light: tokens.secondary.green[400],
            dark: tokens.secondary.green[800],
            contrastText: tokens.white,
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
            default: tokens.white,
            paper: tokens.white,
        },
        text: {
            primary: tokens.neutral[800],
            secondary: tokens.neutral[600],
            disabled: tokens.neutral[400],
        },
        divider: tokens.neutral[100],
    },
    typography: {
        fontFamily: 'Lato, -apple-system, BlinkMacSystemFont, Roboto, Oxygen, Ubuntu, Cantarell, "Fira Sans", "Droid Sans", "Helvetica Neue", Arial, sans-serif',
        h1: {
            fontSize: '2.5rem',
            fontWeight: 700, // bold
            lineHeight: 1.15, // narrow
        },
        h2: {
            fontSize: '2rem',
            fontWeight: 700, // bold
            lineHeight: 1.15, // narrow
        },
        h3: {
            fontSize: '1.75rem',
            fontWeight: 600, // semibold
            lineHeight: 1.33, // base
        },
        h4: {
            fontSize: '1.5rem',
            fontWeight: 600, // semibold
            lineHeight: 1.33, // base
        },
        h5: {
            fontSize: '1.25rem',
            fontWeight: 600, // semibold
            lineHeight: 1.425, // medium
        },
        h6: {
            fontSize: '1rem',
            fontWeight: 600, // semibold
            lineHeight: 1.425, // medium
        },
        subtitle1: {
            fontSize: '1.125rem',
            fontWeight: 400, // regular
            lineHeight: 1.5, // comfortable
        },
        subtitle2: {
            fontSize: '0.875rem',
            fontWeight: 400, // regular
            lineHeight: 1.5, // comfortable
        },
        body1: {
            fontSize: '1rem',
            fontWeight: 400, // regular
            lineHeight: 1.5, // comfortable
        },
        body2: {
            fontSize: '0.875rem',
            fontWeight: 400, // regular
            lineHeight: 1.5, // comfortable
        },
        button: {
            textTransform: 'none',
            fontWeight: 600, // semibold
            lineHeight: 1.33, // base
        },
    },
    components: {
        MuiListItem: {
            styleOverrides: {
                root: ({ theme }) => ({
                    [`& .${svgIconClasses.root}`]: {
                        width: '1rem',
                        height: '1rem',
                        color: (theme.vars || theme).palette.text.secondary,
                    },
                    [`& .${typographyClasses.root}`]: {
                        fontWeight: 600,
                    },
                    [`& .${listItemIconClasses.root}`]: {
                        minWidth: '16px',
                    },
                    [`& .${buttonBaseClasses.root}`]: {
                        display: 'flex',
                        gap: 8,
                        padding: '2px 8px',
                        borderRadius: (theme.vars || theme).shape.borderRadius,
                        opacity: 0.7,
                        fontWeight: 700,
                        '&.Mui-selected': {
                            opacity: 1,
                            color: tokens.primary[600],
                            backgroundColor: tokens.neutral[35],
                            [`& .${svgIconClasses.root}`]: {
                                color: tokens.primary[600],
                            },
                            '&:focus-visible': {
                                backgroundColor: tokens.neutral[35],
                                color: tokens.primary[600],
                            },
                            '&:hover': {
                                backgroundColor: tokens.neutral[35],
                                color: tokens.primary[600],
                            },
                        },
                        '&:focus-visible': {
                            backgroundColor: 'transparent',
                        },
                    },
                }),
            },
        },
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
                    backgroundColor: tokens.white,
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
                            backgroundColor: tokens.white,
                            borderColor: tokens.neutral[100],
                            color: tokens.neutral[300],
                        },
                    },
                    '& .MuiInputBase-input': {
                        padding: '8px 12px',
                        fontSize: '0.875rem',
                        fontWeight: 400,
                        lineHeight: 1.5,
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
                            backgroundColor: tokens.white,
                            borderColor: tokens.neutral[100],
                            color: tokens.neutral[300],
                        },
                        '& .MuiAutocomplete-input': {
                            fontSize: '0.875rem',
                            fontWeight: 400,
                            lineHeight: 1.5,
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
                        fontSize: '0.875rem',
                        fontWeight: 400,
                        lineHeight: 1.5,
                    },
                },
            },
        },
        MuiSelect: {
            styleOverrides: {
                select: {
                    padding: '4px 8px',
                    fontSize: '0.875rem',
                    fontWeight: 400,
                    lineHeight: 1.5,
                    color: tokens.neutral[800],
                    '&:focus': {
                        backgroundColor: 'transparent',
                    },
                },
            },
        },
        MuiDataGrid: {
            styleOverrides: {
                root: ({ theme }) => ({
                    '--DataGrid-overlayHeight': '300px',
                    overflow: 'clip',
                    borderColor: (theme.vars || theme).palette.divider,
                    backgroundColor: tokens.neutral[40],
                    [`& .${gridClasses.columnHeader}`]: {
                        backgroundColor: tokens.neutral[40],
                    },
                    [`& .${gridClasses.footerContainer}`]: {
                        backgroundColor: tokens.neutral[40],
                    },
                    [`& .${checkboxClasses.root}`]: {
                        padding: theme.spacing(0.5),
                        '& > svg': {
                            fontSize: '1rem',
                        },
                    },
                    [`& .${tablePaginationClasses.root}`]: {
                        marginRight: theme.spacing(1),
                        '& .MuiIconButton-root': {
                            maxHeight: 32,
                            maxWidth: 32,
                            '& > svg': {
                                fontSize: '1rem',
                            },
                        },
                    },
                }),
                cell: ({ theme }) => ({ borderTopColor: (theme.vars || theme).palette.divider }),
                menu: ({ theme }) => ({
                    borderRadius: theme.shape.borderRadius,
                    backgroundImage: 'none',
                    [`& .${paperClasses.root}`]: {
                        border: `1px solid ${(theme.vars || theme).palette.divider}`,
                    },

                    [`& .${menuItemClasses.root}`]: {
                        margin: '0 4px',
                    },
                    [`& .${listItemIconClasses.root}`]: {
                        marginRight: 0,
                    },
                    [`& .${listClasses.root}`]: {
                        paddingLeft: 0,
                        paddingRight: 0,
                    },
                }),

                row: ({ theme }) => ({
                    '&:last-of-type': { borderBottom: `1px solid ${(theme.vars || theme).palette.divider}` },
                    '&:hover': {
                        backgroundColor: (theme.vars || theme).palette.action.hover,
                    },
                    '&.Mui-selected': {
                        background: (theme.vars || theme).palette.action.selected,
                        '&:hover': {
                            backgroundColor: (theme.vars || theme).palette.action.hover,
                        },
                    },
                }),
                iconButtonContainer: ({ theme }) => ({
                    [`& .${iconButtonClasses.root}`]: {
                        border: 'none',
                        backgroundColor: 'transparent',
                        '&:hover': {
                            backgroundColor: alpha(theme.palette.action.selected, 0.3),
                        },
                        // '&:active': {
                        //     backgroundColor: gray[200],
                        // },
                        // ...theme.applyStyles('dark', {
                        //     color: gray[50],
                        //     '&:hover': {
                        //         backgroundColor: gray[800],
                        //     },
                        //     '&:active': {
                        //         backgroundColor: gray[900],
                        //     },
                        // }),
                    },
                }),
                menuIconButton: ({ theme }) => ({
                    border: 'none',
                    backgroundColor: 'transparent',
                    // '&:hover': {
                    //     backgroundColor: gray[100],
                    // },
                    // '&:active': {
                    //     backgroundColor: gray[200],
                    // },
                    // ...theme.applyStyles('dark', {
                    //     color: gray[50],
                    //     '&:hover': {
                    //         backgroundColor: gray[800],
                    //     },
                    //     '&:active': {
                    //         backgroundColor: gray[900],
                    //     },
                    // }),
                }),
                filterForm: ({ theme }) => ({
                    gap: theme.spacing(1),
                    alignItems: 'flex-end',
                }),
                columnsManagementHeader: ({ theme }) => ({
                    paddingRight: theme.spacing(3),
                    paddingLeft: theme.spacing(3),
                }),
                columnHeaderTitleContainer: {
                    flexGrow: 1,
                    justifyContent: 'space-between',
                },
                columnHeaderDraggableContainer: { paddingRight: 2 },
            },
        },
    },
    shape: {
        borderRadius: 8,
    },
});
