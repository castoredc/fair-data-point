import React, {Component, FC} from 'react'
import {Link} from "react-router-dom";
import './ListItem.scss'
import {classNames, isURL} from "../../util";
import Tags from "../Tags";
import {Icon} from "@castoredc/matter";
import CustomIcon from "../Icon/CustomIcon";
import {MatterIcon} from "@castoredc/matter-icons";

type ListItemProps = {
    title: string;
    description?: string;
    link?: string;
    icon?: MatterIcon;
    customIcon?: string;
    smallIcon?: boolean;
    fill?: boolean;
    newWindow?: boolean;
    selectable: boolean;
    active?: boolean;
    className?: string;
    onClick?: (e) => void;
    badge?: string;
    tags?: string[];
}

const ListItem: FC<ListItemProps> = ({
                                         title,
                                         description,
                                         link,
                                         icon,
                                         customIcon,
                                         smallIcon,
                                         fill,
                                         newWindow,
                                         selectable = false,
                                         active,
                                         className,
                                         onClick,
                                         badge,
                                         tags = []
                                     }) => {

    if (selectable && onClick) {
        return <a href="#" className={classNames("ListItem", "Selectable", active && 'Active', className)}
                  onClick={(e) => {
                      e.preventDefault();
                      onClick(e);
                  }}>
            {(icon) && <span className={classNames('ListItemLeftIcon', fill && 'Fill')}>
                <Icon type={icon}/>
            </span>}
            {(customIcon) && <span className={classNames('ListItemLeftIcon', fill && 'Fill')}>
                <CustomIcon type={customIcon}/>
            </span>}
            <span className="ListItemTitle">{title}</span>
            <span className="ListItemDescription">{description}</span>
        </a>;
    }

    const children = <span>
            <span className="ListItemHeader">
                <span className="ListItemTitle">{title}</span>
                {icon && smallIcon && <span className="ListItemSmallIcon"><Icon type={icon}/></span>}
                {badge && <span className="ListItemBadge">{badge}</span>}
            </span>
            <span className="ListItemDescription">{description}</span>
        {tags.length > 0 && <Tags tags={tags} className="ListItemTags"/>}
        </span>;

    if (isURL(link) || newWindow) {
        return <a href={link} onClick={onClick} target="_blank" className="ListItem">{children}</a>;
    }

    return <Link to={{ pathname: link }} className="ListItem" onClick={onClick}>{children}</Link>;
}

ListItem.defaultProps = {
    fill: true,
    newWindow: false,
    selectable: false,
    smallIcon: false,
    active: false,
}

export default ListItem;