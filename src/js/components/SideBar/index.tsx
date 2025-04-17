import React, { FC } from 'react';
import { Link, matchPath } from 'react-router-dom';
import BackButton from '../BackButton';
import FormItem from '../Form/FormItem';
import * as H from 'history';
import { Box, Divider, styled } from '@mui/material';
import MuiDrawer, { drawerClasses } from '@mui/material/Drawer';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';

interface SideBarProps {
    location: H.Location;
    items: any;
    back?: any;
    onVersionChange?: (event: SelectChangeEvent) => void;
    history: H.History;
}

const drawerWidth = 240;

const Drawer = styled(MuiDrawer)({
    width: drawerWidth,
    flexShrink: 0,
    boxSizing: 'border-box',
    mt: 10,
    [`& .${drawerClasses.paper}`]: {
        width: drawerWidth,
        boxSizing: 'border-box',
    },
});

const SideBar: FC<SideBarProps> = ({ location, items, back, onVersionChange, history }) => {
    return (
        <Drawer
            variant="permanent"
            sx={{
                display: { xs: 'none', md: 'block' },
                [`& .${drawerClasses.paper}`]: {
                    backgroundColor: 'background.paper',
                },
            }}
        >
            {back && (<>
                    <Box
                        sx={{
                            display: 'flex',
                            mt: 'calc(var(--template-frame-height, 0px) + 4px)',
                            p: 1.5,
                        }}
                    >
                        <BackButton to={back.to} sidebar history={history}>
                            {back.title}
                        </BackButton>
                    </Box>
                    <Divider />
                </>
            )}
            <Box
                sx={{
                    overflow: 'auto',
                    height: '100%',
                    display: 'flex',
                    flexDirection: 'column',
                }}
            >
                <List dense>
                    {items.map((item, index) => {
                        if (typeof item.type !== 'undefined') {
                            if (item.type === 'separator') {
                                return <Divider key={`sitebar-item-${index}`} />;
                            } else if (item.type === 'component') {
                                return item.contents;
                            } else if (item.type === 'version' && onVersionChange) {
                                return (
                                    <FormItem label="Version" className="SideBarNavVersion"
                                              key={`sitebar-item-${index}`}>
                                        <div className="Select">
                                            <Select
                                                onChange={onVersionChange}
                                                value={item.current}
                                                fullWidth
                                            >
                                                {item.versions.map((version: any) => {
                                                    return <MenuItem value={version.value}>{version.label}</MenuItem>
                                                })}
                                            </Select>
                                        </div>
                                    </FormItem>
                                );
                            }
                        } else {
                            const active = !!matchPath(location.pathname, {
                                path: item.to,
                                exact: item.exact,
                                strict: true,
                            });

                            // @ts-ignore
                            return (
                                <ListItem
                                    key={`sitebar-item-${index}`}
                                    disablePadding
                                    sx={{ display: 'block' }}
                                >
                                    <ListItemButton
                                        selected={item.active}
                                        component={Link}
                                        to={item.disabled ? '#' : item.to}
                                        // className={classNames('SideBarNavItem', item.active && 'Active', active && 'Active', item.disabled && 'Disabled')}
                                    >
                                        <ListItemIcon>
                                            {item.icon && item.icon}
                                        </ListItemIcon>
                                        <ListItemText primary={item.title} />
                                    </ListItemButton>
                                </ListItem>
                            );
                        }
                    })}
                </List>
            </Box>
        </Drawer>
    );
};

export default SideBar;
