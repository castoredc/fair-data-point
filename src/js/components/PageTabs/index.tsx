import React, { FC } from 'react';
import Tabs from '@mui/material/Tabs';
import { Box, Tab } from '@mui/material';

interface TabsProps {
    tabs: Record<string, {
        title: string,
        content: React.ReactNode,
    }>,
    selected: string,
    onChange: (tabId: any) => void
}

interface TabPanelProps {
    children?: React.ReactNode;
    index: string;
    value: string;
}

const TabPanel: FC<TabPanelProps> = ({index, value, children}) => {
    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`simple-tabpanel-${index}`}
            aria-labelledby={`simple-tab-${index}`}
        >
            {value === index && <Box sx={{ p: 3 }}>{children}</Box>}
        </div>
    );
}

const PageTabs: FC<TabsProps> = ({ tabs, selected, onChange }) => {
    return (
        <div className="PageTabs">
            <Box sx={{ borderBottom: 1, borderColor: 'divider' }}>
                <Tabs onChange={(event: React.SyntheticEvent, value: any) => onChange(value)} value={selected}>
                    {Object.keys(tabs).map((key) => {
                        return <Tab label={tabs[key].title} value={key} />
                    })}
                </Tabs>
            </Box>

            {Object.keys(tabs).map((key) => {
                return <TabPanel key={key} value={selected} index={key}>
                    {tabs[key].content}
                </TabPanel>
            })}
        </div>
    );
};

export default PageTabs;
