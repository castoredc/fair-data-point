import React, {Component} from 'react'

import './MetadataItem.scss'

class MetadataItem extends Component {
    render() {
        const { label, value } = this.props;

        return <div className="MetadataItem">
            <div className="MetadataItemLabel">{label}</div>
            <div className="MetadataItemValue">{value}</div>
        </div>;
    }
}

export default MetadataItem