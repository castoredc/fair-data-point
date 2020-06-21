import React, {Component} from 'react'
import {Link} from "react-router-dom";
import './ListItem.scss'
import {classNames, isURL} from "../../util";
import Tags from "../Tags";
import {Icon} from "@castoredc/matter";

class ListItem extends Component {
    render() {
        const {   title,
                  description,
                  link, leftIcon,
                  smallIcon,
                  fill = true,
                  newWindow = false,
                  selectable = false,
                  active = false,
                  className,
                  onClick = () => {},
                  badge,
                  tags = []
              }  = this.props;

        if(selectable)
        {
            return <a href="#" className={classNames("ListItem", "Selectable", active && 'Active', className)} onClick={onClick}>
                {leftIcon && <span className={classNames('ListItemLeftIcon', fill && 'Fill')}><Icon type={leftIcon} /></span>}
                <span className="ListItemTitle">{title}</span>
                <span className="ListItemDescription">{description}</span>
            </a>;
        }

        const children = <span>
            <span className="ListItemHeader">
                <span className="ListItemTitle">{title}</span>
                {smallIcon && <span className="ListItemSmallIcon"><Icon type={smallIcon} /></span>}
                {badge && <span className="ListItemBadge">{badge}</span>}
            </span>
            <span className="ListItemDescription">{description}</span>
            {tags.length > 0 && <Tags tags={tags} className="ListItemTags" />}
        </span>;

        if(isURL(link) || newWindow)
        {
            return <a href={link} target="_blank" className="ListItem">{children}</a>;
        }

        return <Link to={link} className="ListItem" onClick={onClick}>{children}</Link>;
    }
}

export default ListItem