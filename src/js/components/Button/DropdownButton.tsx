import React, { FC, useState, MouseEvent } from 'react';
import {
    Button,
    IconButton,
    Menu,
    MenuItem,
    ListItemIcon,
    ListItemText,
    useMediaQuery,
    useTheme,
} from '@mui/material';
import AccountCircleIcon from '@mui/icons-material/AccountCircle';
import KeyboardArrowDownIcon from '@mui/icons-material/KeyboardArrowDown';

interface DropdownButtonItem {
    label: string;
    icon?: React.ReactNode;
    destination: string;
}

interface DropdownButtonProps {
    text: string;
    items: DropdownButtonItem[];
    icon?: string;
    buttonType?: 'primary' | 'secondary' | 'text';
}

const DropdownButton: FC<DropdownButtonProps> = ({
                                                     text,
                                                     items,
                                                     icon = 'account',
                                                     buttonType = 'primary',
                                                 }) => {
    const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);
    const open = Boolean(anchorEl);
    const theme = useTheme();
    const isMobile = useMediaQuery(theme.breakpoints.down('sm'));

    const handleClick = (event: MouseEvent<HTMLElement>) => {
        setAnchorEl(event.currentTarget);
    };

    const handleClose = () => {
        setAnchorEl(null);
    };

    const handleItemClick = (item: DropdownButtonItem) => {
        window.location.href = item.destination;
        handleClose();
    };

    if (isMobile) {
        return (
            <>
                <IconButton
                    onClick={handleClick}
                    color={buttonType === 'primary' ? 'primary' : 'default'}
                    size="large"
                >
                    <AccountCircleIcon />
                </IconButton>
                <Menu
                    anchorEl={anchorEl}
                    open={open}
                    onClose={handleClose}
                    onClick={handleClose}
                    PaperProps={{
                        elevation: 3,
                        sx: {
                            mt: 1,
                            minWidth: 200,
                            '& .MuiMenuItem-root': {
                                py: 1,
                                px: 2,
                            },
                        },
                    }}
                    transformOrigin={{ horizontal: 'right', vertical: 'top' }}
                    anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}
                >
                    {items.map((item, index) => (
                        <MenuItem
                            key={index}
                            onClick={() => handleItemClick(item)}
                            sx={{
                                '&:hover': {
                                    bgcolor: 'action.hover',
                                },
                            }}
                        >
                            {item.icon && (
                                <ListItemIcon>
                                    {item.icon} aaa
                                </ListItemIcon>
                            )}
                            <ListItemText primary={item.label} />
                        </MenuItem>
                    ))}
                </Menu>
            </>
        );
    }

    return (
        <>
            <Button
                onClick={handleClick}
                variant={buttonType === 'text' ? 'text' : 'contained'}
                color={buttonType === 'primary' ? 'primary' : 'inherit'}
                endIcon={<KeyboardArrowDownIcon />}
                startIcon={<AccountCircleIcon />}
            >
                {text}
            </Button>
            <Menu
                anchorEl={anchorEl}
                open={open}
                onClose={handleClose}
                onClick={handleClose}
                PaperProps={{
                    elevation: 3,
                    sx: {
                        mt: 1,
                        minWidth: 200,
                        '& .MuiMenuItem-root': {
                            py: 1,
                            px: 2,
                        },
                    },
                }}
                transformOrigin={{ horizontal: 'right', vertical: 'top' }}
                anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}
            >
                {items.map((item, index) => (
                    <MenuItem
                        key={index}
                        onClick={() => handleItemClick(item)}
                        sx={{
                            '&:hover': {
                                bgcolor: 'action.hover',
                            },
                        }}
                    >
                        {item.icon && (
                            <ListItemIcon>
                                {item.icon}
                            </ListItemIcon>
                        )}
                        <ListItemText primary={item.label} />
                    </MenuItem>
                ))}
            </Menu>
        </>
    );
};

export default DropdownButton;
