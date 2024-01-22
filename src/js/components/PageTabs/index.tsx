import React, { FC } from 'react';
import { TabsProps } from '@castoredc/matter/lib/types/src/Tabs/Tabs';
import { Tabs } from '@castoredc/matter';
import './PageTabs.scss';

const PageTabs: FC<TabsProps> = ({ tabs, selected, onChange }) => {
    return (
        <div className="PageTabs">
            <Tabs
                tabs={tabs}
                selected={selected}
                onChange={onChange}
            />
        </div>
    );
};

export default PageTabs;
