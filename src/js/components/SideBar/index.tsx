import React, { FC } from 'react';
import { Link, matchPath } from 'react-router-dom';
import BackButton from '../BackButton';
import FormItem from '../Form/FormItem';
import * as H from 'history';
import { Box, Divider, IconButton, Stack, styled, Typography } from '@mui/material';
import MuiDrawer, { drawerClasses } from '@mui/material/Drawer';
import List from '@mui/material/List';
import ListItem from '@mui/material/ListItem';
import ListItemButton from '@mui/material/ListItemButton';
import ListItemIcon from '@mui/material/ListItemIcon';
import ListItemText from '@mui/material/ListItemText';
import Select, { SelectChangeEvent } from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';
import { UserType } from 'types/UserType';
import Logout from '@mui/icons-material/Logout';
import Avatar from 'react-avatar';

interface SideBarProps {
    location: H.Location;
    items: any;
    back?: any;
    onVersionChange?: (version) => void;
    history: H.History;
    user: UserType | null;
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

const SideBar: FC<SideBarProps> = ({ location, items, back, onVersionChange, history, user }) => {
    return (
        <Drawer
            variant="permanent"
            sx={{
                [`& .${drawerClasses.paper}`]: {
                    backgroundColor: 'grey.50',
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
            <Stack sx={{ flexGrow: 1, py: 1, px: 2, justifyContent: 'space-between' }}>
                <List dense>
                    {items.map((item, index) => {
                        if (typeof item.type !== 'undefined') {
                            if (item.type === 'separator') {
                                return <Divider
                                    key={`sitebar-item-${index}`}
                                    sx={{
                                        mt: 1,
                                        mb: 1,
                                    }}
                                />;
                            } else if (item.type === 'component') {
                                return item.contents;
                            } else if (item.type === 'version' && onVersionChange) {
                                return (
                                    <FormItem label="Version" className="SideBarNavVersion"
                                              key={`sitebar-item-${index}`}>
                                        <div className="Select">
                                            <Select
                                                onChange={(event: SelectChangeEvent) => onVersionChange(event.target.value)}
                                                value={item.current.value}
                                                fullWidth
                                            >
                                                {item.versions.map((version: any) => {
                                                    return <MenuItem
                                                        key={version.value}
                                                        value={version.value}
                                                    >
                                                        {version.label}
                                                    </MenuItem>;
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
                                    sx={{ display: 'block', mb: 0.5 }}
                                >
                                    <ListItemButton
                                        selected={active}
                                        component={Link}
                                        to={item.disabled ? '#' : item.to}
                                        disabled={item.disabled}
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
            </Stack>
            <Box sx={{
                borderTop: '1px solid',
                borderColor: 'divider',
                p: 2,
                display: 'flex',
                alignItems: 'center',
                gap: 1,
            }}>
                <Avatar name={user?.details?.fullName} size="32px" round />

                <Box sx={{ mr: 'auto' }}>
                    <Typography variant="body2" sx={{ fontWeight: 500, lineHeight: '16px' }}>
                        {user?.details?.fullName}
                    </Typography>
                </Box>
                <IconButton component="a" href="/logout" color="primary" size="small">
                    <Logout fontSize="small" />
                </IconButton>
            </Box>
        </Drawer>
    );
};

export default SideBar;
