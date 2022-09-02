import React, { Component } from 'react';

import './MetadataItem.scss';
import { classNames, isURL } from '../../util';
import moment from 'moment';

class MetadataItem extends Component {
    render() {
        const { label, url, value, children, className, type = 'text', table } = this.props;

        if (children) {
            return (
                <div className={classNames('MetadataItem', className, table && 'Table')}>
                    <div className="MetadataItemLabel">{label}</div>
                    <div className="MetadataItemValue">{children}</div>
                </div>
            );
        }

        let display = <span>{value}</span>;

        if (isURL(value)) {
            display = (
                <a href={value} target="_blank">
                    {value}
                </a>
            );
        } else if (url) {
            display = (
                <a href={url} target="_blank">
                    {value}
                </a>
            );
        } else if (type === 'date') {
            let date = moment(value.date);
            display = <span>{date.format('MMMM D, YYYY [at] HH:mm')}</span>;
        }

        return (
            <div className={classNames('MetadataItem', className, table && 'Table')}>
                <div className="MetadataItemLabel">{label}</div>
                <div className="MetadataItemValue">{display}</div>
            </div>
        );
    }
}

export default MetadataItem;
