import React, {Component} from 'react'
import { Link } from "react-router-dom";

import './ListItem.scss'
import {isURL} from "../../util";

class ListItem extends Component {
    render() {
        const { title, description, link } = this.props;

        if(isURL(link))
        {
            return <a href={link} target="_blank" className="ListItem">
                <span className="ListItemTitle">{title}</span>
                <span className="ListItemDescription">{description}</span>
            </a>;
        }

        return <Link to={link} className="ListItem">
            <span className="ListItemTitle">{title}</span>
            <span className="ListItemDescription">{description}</span>
        </Link>;
    }
}

export default ListItem