import React from 'react';
import './AssociatedItemsBar.scss';
import AssociatedItemsBarItem from 'components/AssociatedItemsBar/AssociatedItemsBarItem';

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
        <div className="AssociatedItemsBar">
            {content.map(item => (
                <AssociatedItemsBarItem key={item.type} count={item.count} type={item.type} current={current} onClick={onClick} />
            ))}
        </div>
    );
};

export default AssociatedItemsBar;
