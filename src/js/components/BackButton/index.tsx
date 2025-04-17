import React, { FC } from 'react';
import { useHistory } from 'react-router-dom';
import * as H from 'history';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import Button from '@mui/material/Button';

export type BackButtonProps =
    | {
    to?: string | (() => void);
    returnButton: true; // If returnButton is true, history can be optional
    children?: React.ReactNode;
    sidebar?: boolean;
    history?: undefined;
}
    | {
    to?: string | (() => void);
    returnButton?: undefined;
    children?: React.ReactNode;
    sidebar?: boolean;
    history: H.History;
};

const BackButton: FC<BackButtonProps> = ({ to, returnButton, children, sidebar, history }) => {
    let onClickFunction: () => any;

    if (returnButton) {
        let history = useHistory();
        onClickFunction = () => history.go(-1);
    } else if (typeof to === 'string') {
        onClickFunction = () => {
            history.push(to as string);
        };
    } else if (to !== undefined) {
        onClickFunction = () => to();
    } else {
        return null;
    }

    return (
        <Button variant="text"
                onClick={onClickFunction}
                startIcon={<ArrowBackIcon />}
        >
            {children}
        </Button>
    );
};

export default BackButton;
