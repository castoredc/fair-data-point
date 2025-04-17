import React, { FC } from 'react';
import { Link } from 'react-router-dom';
import { classNames, isURL } from '../../util';
import Tags from '../Tags';
import { LocationState } from 'history';
import ListItemButton from '@mui/material/ListItemButton';
import { Divider } from '@mui/material';
import ListItemText from '@mui/material/ListItemText';
import ListItemIcon from '@mui/material/ListItemIcon';

type ListItemProps = {
    title: string;
    description?: string;
    link?: string;
    state?: LocationState;
    icon?: React.ReactNode;
    smallIcon?: boolean;
    fill?: boolean;
    newWindow?: boolean;
    selectable?: boolean;
    active?: boolean;
    className?: string;
    onClick?: (e) => void;
    badge?: string;
    tags?: string[];
    disabled?: boolean;
};

const ListItem: FC<ListItemProps> = ({
                                         title,
                                         description,
                                         link,
                                         state,
                                         icon,
                                         smallIcon = false,
                                         fill = true,
                                         newWindow = false,
                                         selectable = false,
                                         active = false,
                                         className,
                                         onClick,
                                         badge,
                                         tags = [],
                                         disabled = false,
                                     }) => {
    if (selectable && onClick) {
        return (
            <a
                href="#"
                className={classNames('ListItem', 'Selectable', active && 'Active', className)}
                onClick={e => {
                    e.preventDefault();
                    onClick(e);
                }}
            >
                {icon && (
                    <span className={classNames('ListItemLeftIcon', fill && 'Fill')}>
                        {icon}
                    </span>
                )}
                <span className="ListItemTitle">{title}</span>
                <span className="ListItemDescription">{description}</span>
            </a>
        );
    }

    const children = (
        <>
            <ListItemText
                primary={title}
                secondary={description}
            />

            {icon && smallIcon && (
                <ListItemIcon>
                    {icon}
                </ListItemIcon>
            )}
            {/*{badge && <span className="ListItemBadge">{badge}</span>}*/}
            {/*{tags.length > 0 && <Tags tags={tags} className="ListItemTags" /> */}
        </>
    );

    if (disabled) {
        return <div className="ListItem Disabled">{children}</div>;
    }

    let button: React.ReactElement | null = null;

    if (isURL(link) || newWindow) {
        button = <ListItemButton
                component="a"
                href={link}
                onClick={onClick}
                target="_blank"
            >
                {children}
            </ListItemButton>;
    } else {
        button = <ListItemButton
            component={Link}
            to={{ pathname: link, state: state }}
            // className="ListItem"
            onClick={onClick}>
            {children}
        </ListItemButton>
    }

    // @ts-ignore
    return <>
        {button}
        <Divider />
    </>;
};

export default ListItem;
