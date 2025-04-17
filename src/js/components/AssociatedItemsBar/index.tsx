import React from 'react';
import Tabs from '@mui/material/Tabs';
import { Badge, Tab } from '@mui/material';

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
        <Tabs
            value={content.findIndex(item => item.type === current)}
            onChange={onClick ? (event: React.SyntheticEvent, newValue: number) => onClick(content[newValue].type) : undefined}
        >
            {content.map(item => (
                <Tab
                    disabled={current !== item.type && item.count === 0}
                    label={(
                        <Badge badgeContent={item.count}  color="primary">
                            {item.type}
                        </Badge>
                    )}
                />
            ))}
        </Tabs>
    );
};

export default AssociatedItemsBar;
