import React, {Component} from 'react'

import './MetadataItem.scss'
import {isURL} from "../../util";

class MetadataItem extends Component {
    render() {
        const { label, value, children } = this.props;

        if(children)
        {
            return <div className="MetadataItem">
                <div className="MetadataItemLabel">{label}</div>
                <div className="MetadataItemValue">{children}</div>
            </div>;
        }

        let display = <span>{value}</span>;
        if(isURL(value))
        {
            display = <a href={value} target="_blank">{value}</a>;
        }

        return <div className="MetadataItem">
            <div className="MetadataItemLabel">{label}</div>
            <div className="MetadataItemValue">{display}</div>
        </div>;
    }
}

export default MetadataItem