import React, { FC, useState } from 'react';
import { useHistory } from 'react-router-dom';
import { Box, Chip, Paper, Stack, Tab, Tabs, Typography } from '@mui/material';

type Tab = {
    type?: 'separator';
    number?: number;
    title: string;
    badge?: React.ReactNode;
    content: React.ReactNode;
    tag?: string;
    id?: string;
};

type SideTabsProps = {
    tabs: Tab[];
    hasButtons?: boolean;
    hasTabs?: boolean;
    title?: React.ReactNode;
    actions?: React.ReactNode;
    initialTab?: number;
    url?: string;
};

const SideTabs: FC<SideTabsProps> = ({
                                         tabs,
                                         hasButtons = false,
                                         hasTabs = false,
                                         title,
                                         actions,
                                         initialTab,
                                         url,
                                     }) => {
    const [activeTab, setActiveTab] = useState(initialTab ?? 0);
    let history = useHistory();

    const changeTab = (index: number) => {
        setActiveTab(index);

        if (url) {
            history.push(`${url}/${tabs[index].id}`);
        }
    };

    return (
        <Box sx={{ display: 'flex', height: '100%', overflow: 'auto' }}>
            <Paper
                elevation={0}
                sx={{
                    width: 240,
                    flexShrink: 0,
                    mr: 4,
                    display: 'flex',
                    flexDirection: 'column',
                    borderRight: 1,
                    borderColor: 'divider',
                }}
            >
                {(title || actions) && (
                    <Box sx={{ p: 1, mb: 1, borderBottom: 1, borderColor: 'divider' }}>
                        <Stack direction="row" sx={{ justifyContent: 'space-between', alignItems: 'center' }}>
                            {title && (
                                <Typography variant="subtitle1" sx={{ fontWeight: 'bold', color: 'text.primary' }}>
                                    {title}
                                </Typography>
                            )}
                            {actions && <Box>{actions}</Box>}
                        </Stack>
                    </Box>
                )}
                <Tabs
                    orientation="vertical"
                    value={activeTab}
                    onChange={(_, value) => changeTab(value)}
                    sx={{
                        borderRight: 1,
                        borderColor: 'divider',
                        '& .MuiTab-root': {
                            minHeight: 48,
                            alignItems: 'flex-start',
                            textAlign: 'left',
                            px: 2,
                        },
                    }}
                >
                    {tabs.map((tab, index) => {
                        if (typeof tab.type !== 'undefined' && tab.type === 'separator') {
                            return <Box key={`sidetabs-${index}`}
                                        sx={{ my: 1, borderTop: 1, borderColor: 'divider' }} />;
                        } else {
                            return (
                                <Tab
                                    key={`sidetabs-${index}`}
                                    value={index}
                                    label={
                                        <Stack direction="row" spacing={1} alignItems="center" sx={{ width: '100%' }}>
                                            {tab.number && (
                                                <Typography variant="body2" component="span">
                                                    {tab.number}
                                                </Typography>
                                            )}
                                            <Typography
                                                variant="body2"
                                                component="span"
                                                sx={{
                                                    flex: 1,
                                                    textAlign: 'left',
                                                    whiteSpace: 'normal',
                                                    wordBreak: 'break-word',
                                                }}
                                            >
                                                {tab.title}
                                            </Typography>
                                            {tab.badge && (
                                                <Chip
                                                    size="small"
                                                    sx={{
                                                        textTransform: 'uppercase',
                                                        fontSize: '0.5rem',
                                                    }}
                                                    label={tab.badge}
                                                />
                                            )}
                                        </Stack>
                                    }
                                    sx={{
                                        width: '100%',
                                        justifyContent: 'flex-start',
                                    }}
                                />
                            );
                        }
                    })}
                </Tabs>
            </Paper>
            <Box sx={{ flex: 1, overflow: 'auto' }}>
                {tabs[activeTab] !== undefined && tabs[activeTab].content}
            </Box>
        </Box>
    );
};

export default SideTabs;
