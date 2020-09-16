import React, {Component} from 'react'
import {classNames} from "../../util";
import './SideBar.scss';
import {Dropdown as CastorDropdown, Icon} from "@castoredc/matter";
import {Link, matchPath} from "react-router-dom";
import CustomIcon from "../Icon/CustomIcon";
import BackButton from "../BackButton";
import FormItem from "../Form/FormItem";
import ScrollShadow from "../ScrollShadow";

export default class SideBar extends Component {
    constructor(props) {
        super(props);
    }

    render() {
        const {location, items, back, onVersionChange} = this.props;

        return <div className="SideBar">
            {back && <div className="Back">
                <BackButton to={back.to}>{back.title}</BackButton>
            </div>}
            <ScrollShadow className="SideBarScrollShadow">
                <div className="SideBarNav">
                    {items.map((item, index) => {
                        if (typeof item.type !== 'undefined') {
                            if (item.type === 'separator') {
                                return <hr key={`sitebar-item-${index}`}/>;
                            } else if (item.type === 'component') {
                                return item.contents;
                            } else if (item.type === 'version') {
                                return <FormItem label="Version" className="SideBarNavVersion">
                                    <div className="Select">
                                        <CastorDropdown
                                            onChange={onVersionChange}
                                            value={item.current}
                                            options={item.versions}
                                            menuPlacement="auto"
                                            width="fullWidth"
                                        />
                                    </div>
                                </FormItem>
                            }
                        } else {
                            const active = (!!matchPath(location.pathname, {
                                path: item.to,
                                exact: item.exact,
                                strict: true
                            }));

                            return <Link
                                to={item.to}
                                exact={item.exact}
                                className={classNames('SideBarNavItem', item.active && 'Active', active && 'Active', item.disabled && 'Disabled')}
                                key={`sidetabs-${index}`}>
                            <span className="SideBarNavItemIcon">
                                {item.icon && <Icon type={item.icon}/>}
                                {item.customIcon && <CustomIcon type={item.customIcon}/>}
                            </span>
                                {item.title}
                            </Link>
                        }
                    })}
                </div>
            </ScrollShadow>
        </div>;
    }
}