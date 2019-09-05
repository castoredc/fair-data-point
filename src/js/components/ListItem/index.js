import React, {Component} from 'react'

import './ListItem.scss'

class ListItem extends Component {
    render() {
        const { title, description, link } = this.props;

        return <a href={link} className="ListItem">
            <span className="ListItemTitle">{title}</span>
            <span className="ListItemDescription">{description}</span>
        </a>;
    }
}

export default ListItem