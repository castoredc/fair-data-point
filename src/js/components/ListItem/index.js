import React, {Component} from 'react'
import { Link } from "react-router-dom";
import Icon from "../Icon";
import './ListItem.scss'
import {isURL} from "../../util";

class ListItem extends Component {
    render() {
        const { title, description, link, smallIcon } = this.props;

        if(isURL(link))
        {
            return <a href={link} target="_blank" className="ListItem">
                {smallIcon && <span className="ListItemIcon"><Icon type={smallIcon} /></span>}
                <span className="ListItemTitle">{title}</span>
                <span className="ListItemDescription">{description}</span>
            </a>;
        }

        return <Link to={link} className="ListItem">
            {smallIcon && <span className="ListItemIcon"><Icon type={smallIcon} /></span>}
            <span className="ListItemTitle">{title}</span>
            <span className="ListItemDescription">{description}</span>
        </Link>;
    }
}

export default ListItem