import React, {Component} from 'react'
import {Link} from "react-router-dom";
import Icon from "../Icon";
import './ListItem.scss'
import {classNames, isURL} from "../../util";

class ListItem extends Component {
    render() {
        const { title, description, link, leftIcon, smallIcon, newWindow = false, selectable = false, active = false, onClick = () => {}}  = this.props;

        if(selectable)
        {
            return <a href="#" className={classNames("ListItem", "Selectable", active && 'Active')} onClick={onClick}>
                {leftIcon && <span className="ListItemLeftIcon"><Icon type={leftIcon} /></span>}
                <span className="ListItemTitle">{title}</span>
                <span className="ListItemDescription">{description}</span>
            </a>;
        }

        if(isURL(link) || newWindow)
        {
            return <a href={link} target="_blank" className="ListItem">
                {smallIcon && <span className="ListItemSmallIcon"><Icon type={smallIcon} /></span>}
                <span className="ListItemTitle">{title}</span>
                <span className="ListItemDescription">{description}</span>
            </a>;
        }

        return <Link to={link} className="ListItem" onClick={onClick}>
            {smallIcon && <span className="ListItemSmallIcon"><Icon type={smallIcon} /></span>}
            <span className="ListItemTitle">{title}</span>
            <span className="ListItemDescription">{description}</span>
        </Link>;
    }
}

export default ListItem