import React from 'react';
import './AssociatedItemsBar.scss';
import { classNames } from '../../util';

interface AssociatedItemsBarItemProps {
    count: number;
    onClick?: (type: string) => void;
    type: string;
    current: string;
}

const AssociatedItemsBarItem: React.FC<AssociatedItemsBarItemProps> = ({ count, onClick, type, current }) => {
    return (
        <button
            className={classNames('AssociatedItemsBarItem', current === type && 'active')}
            onClick={() => onClick && onClick(type)}
            disabled={current !== type && count === 0}
        >
            {Item[type as keyof typeof Item]}

            <span>{count}</span>
        </button>
    );
};

export default AssociatedItemsBarItem;

const Item = {
    study: 'Studies',
    catalog: 'Catalogs',
    dataset: 'Datasets',
    distribution: 'Distributions',
};
