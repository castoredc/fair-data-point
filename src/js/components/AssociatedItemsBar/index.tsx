import React from 'react';
import Tabs from '@mui/material/Tabs';
import { Badge, Divider, Tab } from '@mui/material';

interface AssociatedItemsBarProps {
    items: { [key: string]: number };
    current: string;
    onClick?: (type: string) => void;
}

const AssociatedItemsBar: React.FC<AssociatedItemsBarProps> = ({ items, current, onClick }) => {
    const withContent: { type: string; count: number }[] = [];
    const withoutContent: { type: string; count: number }[] = [];

    Object.entries(items).forEach(([key, value]) => {
        const object = { type: key, count: value };

        if (value > 0) {
            withContent.push(object);
        } else {
            withoutContent.push(object);
        }
    });

    const content = withContent.concat(withoutContent);

    return (
        <>
            <Tabs
                value={content.findIndex(item => item.type === current)}
                onChange={onClick ? (event: React.SyntheticEvent, newValue: number) => onClick(content[newValue].type) : undefined}
            >
                {content.map(item => (
                    <Tab
                        key={item.type}
                        disabled={current !== item.type && item.count === 0}
                        label={(
                            <div style={{ display: 'flex', alignItems: 'center', gap: '4px' }}>
                                {Item[item.type as keyof typeof Item]}
                                {item.count > 0 && <Badge
                                    badgeContent={item.count}
                                    color="primary"
                                    sx={{
                                        '& .MuiBadge-badge': {
                                            position: 'relative',
                                            transform: 'none',
                                        },
                                    }}
                                />}
                            </div>
                        )}
                    />
                ))}
            </Tabs>
            <Divider />
        </>
    );
};

export default AssociatedItemsBar;

const Item = {
    study: 'Studies',
    catalog: 'Catalogs',
    dataset: 'Datasets',
    distribution: 'Distributions',
};