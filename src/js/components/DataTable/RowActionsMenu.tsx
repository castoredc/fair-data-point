import { GridRowModel } from '@mui/x-data-grid/models/gridRows';
import React, { type MouseEvent } from 'react';
import { IconButton, Menu, MenuItem } from '@mui/material';
import MoreVertIcon from '@mui/icons-material/MoreVert';

type MenuItem = {
    label: string;
    destination: ((event?: MouseEvent) => void);
}

interface RowActionsMenuProps {
    row?: GridRowModel,
    items: MenuItem[],
}

export const RowActionsMenu: React.FC<RowActionsMenuProps> = ({ row, items }) => {
    const [anchorEl, setAnchorEl] = React.useState(null);
    const open = Boolean(anchorEl);

    const handleClick = (event) => {
        setAnchorEl(event.currentTarget);
    };
    const handleClose = () => {
        setAnchorEl(null);
    };

    return (
        <>
            <IconButton
                aria-controls={open ? 'actions-menu' : undefined}
                aria-haspopup="true"
                aria-expanded={open ? 'true' : undefined}
                onClick={handleClick}
            >
                <MoreVertIcon />
            </IconButton>
            <Menu
                id="actions-menu"
                anchorEl={anchorEl}
                open={open}
                onClose={handleClose}
            >
                {items.map((item, key) => <MenuItem key={key} onClick={item.destination}>{item.label}</MenuItem>)}
            </Menu>
        </>
    );
};