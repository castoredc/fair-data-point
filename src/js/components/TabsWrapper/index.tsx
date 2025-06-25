import React, { FC } from 'react';
import { TabsProps } from '@castoredc/matter/lib/types/src/Tabs/Tabs';
import { Tabs } from '@castoredc/matter';
import './TabsWrapper.scss';

const TabsWrapper: FC<TabsProps> = ({ tabs, selected, onChange }) => {
    return (
        <div className="TabsWrapper">
            <Tabs tabs={tabs} selected={selected} onChange={onChange} />
        </div>
    );
};

export default TabsWrapper;
