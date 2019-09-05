import React, {Component} from 'react'
import { Link } from "react-router-dom";

import './ListItem.scss'

class ListItem extends Component {
    render() {
        const { title, description, link } = this.props;

        return <Link to={link} className="ListItem">
            <span className="ListItemTitle">{title}</span>
            <span className="ListItemDescription">{description}</span>
        </Link>;
    }
}

export default ListItem