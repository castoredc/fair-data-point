import React, {Component} from 'react'

import './MetadataItem.scss'
import {classNames, isURL} from "../../util";

class MetadataItem extends Component {
    render() {
        const { label, url, value, children, className } = this.props;

        if(children)
        {
            return <div className={classNames('MetadataItem', className)}>
                <div className="MetadataItemLabel">{label}</div>
                <div className="MetadataItemValue">{children}</div>
            </div>;
        }

        let display = <span>{value}</span>;
        if(isURL(value))
        {
            display = <a href={value} target="_blank">{value}</a>;
        }
        if(url)
        {
            display = <a href={url} target="_blank">{value}</a>;
        }

        return <div className={classNames('MetadataItem', className)}>
            <div className="MetadataItemLabel">{label}</div>
            <div className="MetadataItemValue">{display}</div>
        </div>;
    }
}

export default MetadataItem