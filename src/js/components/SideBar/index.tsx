import React, { FC } from 'react';
import { classNames } from '../../util';
import './SideBar.scss';
import { Dropdown as CastorDropdown, Icon } from '@castoredc/matter';
import { Link, matchPath } from 'react-router-dom';
import CustomIcon from '../Icon/CustomIcon';
import BackButton from '../BackButton';
import FormItem from '../Form/FormItem';
import ScrollShadow from '../ScrollShadow';
import * as H from 'history';
import { ActionMeta } from 'react-select/src/types';

interface SideBarProps {
    location: H.Location;
    items: any;
    back?: any;
    onVersionChange?: (value: any, action: ActionMeta<any>) => void;
    history: H.History;
}

const SideBar: FC<SideBarProps> = ({ location, items, back, onVersionChange, history }) => {
    return (
        <div className="SideBar">
            {back && (
                <div className="Back">
                    <BackButton to={back.to} sidebar history={history}>
                        {back.title}
                    </BackButton>
                </div>
            )}
            <ScrollShadow className="SideBarScrollShadow">
                <div className="SideBarNav">
                    {items.map((item, index) => {
                        if (typeof item.type !== 'undefined') {
                            if (item.type === 'separator') {
                                return <hr key={`sitebar-item-${index}`} />;
                            } else if (item.type === 'component') {
                                return item.contents;
                            } else if (item.type === 'version' && onVersionChange) {
                                return (
                                    <FormItem label="Version" className="SideBarNavVersion" key={`sitebar-item-${index}`}>
                                        <div className="Select">
                                            <CastorDropdown
                                                onChange={onVersionChange}
                                                value={item.current}
                                                options={item.versions}
                                                menuPlacement="auto"
                                                width="fullWidth"
                                                getOptionLabel={({ label }) => label}
                                                getOptionValue={({ value }) => value}
                                            />
                                        </div>
                                    </FormItem>
                                );
                            }
                        } else {
                            const active = !!matchPath(location.pathname, {
                                path: item.to,
                                exact: item.exact,
                                strict: true,
                            });

                            // @ts-ignore
                            return (
                                <Link
                                    to={item.disabled ? '#' : item.to}
                                    className={classNames('SideBarNavItem', item.active && 'Active', active && 'Active', item.disabled && 'Disabled')}
                                    key={`sitebar-item-${index}`}
                                >
                                    <span className="SideBarNavItemIcon">
                                        {item.icon && <Icon type={item.icon} />}
                                        {item.customIcon && <CustomIcon type={item.customIcon} />}
                                    </span>
                                    <span className="SideBarNavItemTitle">{item.title}</span>
                                </Link>
                            );
                        }
                    })}
                </div>
            </ScrollShadow>
        </div>
    );
};

export default SideBar;
