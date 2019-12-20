import React, {Component} from 'react'
import Icon from "../../components/Icon";

import './MetadataItem.scss'

class Contact extends Component {
    render() {
        const { name, url, type } = this.props;

        let iconType = 'users';
        if(type === 'organization') {
            iconType = 'globe';
        }

        return <a href={url} className="Contact" target="_blank">
            <Icon type={iconType} />
            {name}
        </a>;
    }
}

export default Contact