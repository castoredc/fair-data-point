import React, { Component } from 'react';
import './AssociatedItemsBar.scss';
import { classNames } from '../../util';

class AssociatedItemsBarItem extends Component {
    render() {
        const { count, onClick, type, current } = this.props;

        return (
            <button
                className={classNames('AssociatedItemsBarItem', current === type && 'active')}
                onClick={() => onClick(type)}
                disabled={current !== type && count === 0}
            >
                {Item[type]}

                <span>{count}</span>
            </button>
        );
    }
}

export default class AssociatedItemsBar extends Component {
    render() {
        const { items, current, onClick } = this.props;

        let withContent = [];
        let withoutContent = [];

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
                {content.map(item => {
                    return <AssociatedItemsBarItem key={item.type} count={item.count} type={item.type} current={current} onClick={onClick} />;
                })}
            </div>
        );
    }
}

const Item = {
    study: 'Studies',
    catalog: 'Catalogs',
    dataset: 'Datasets',
    distribution: 'Distributions',
};
