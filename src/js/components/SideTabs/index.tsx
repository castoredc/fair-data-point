import React, { FC, useState } from 'react';
import { classNames } from '../../util';
import './SideTabs.scss';
import ScrollShadow from '../ScrollShadow';
import { useHistory } from 'react-router-dom';
import Stack from '@mui/material/Stack';

type Tab = {
    type?: 'separator';
    number?: number;
    title: string;
    badge?: React.ReactNode;
    content: React.ReactNode;
    tag?: string;
    id?: string;
};

type SideTabsProps = {
    tabs: Tab[];
    hasButtons?: boolean;
    hasTabs?: boolean;
    title?: React.ReactNode;
    actions?: React.ReactNode;
    initialTab?: number;
    url?: string;
};

const SideTabs: FC<SideTabsProps> = ({
                                         tabs,
                                         hasButtons = false,
                                         hasTabs = false,
                                         title,
                                         actions,
                                         initialTab,
                                         url,
                                     }) => {
    const [activeTab, setActiveTab] = useState(initialTab ?? 0);
    let history = useHistory();

    const changeTab = (index: number) => {
        setActiveTab(index);

        if (url) {
            history.push(`${url}/${tabs[index].id}`);
        }
    };

    return (
        <div className={classNames('SideTabs', hasButtons && 'HasButtons', title && 'HasTitle', hasTabs && 'HasTabs')}>
            <div className="SideTabsNav">
                {(title || actions) && (
                    <div className="SideTabsHeader">
                        <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                            {title && <div className="SideTabsHeaderTitle">{title}</div>}
                            {actions && <div className="SideTabsHeaderActions">{actions}</div>}
                        </Stack>
                    </div>
                )}
                <ScrollShadow className="SideTabsScrollable">
                    {tabs.map((tab, index) => {
                        if (typeof tab.type !== 'undefined') {
                            if (tab.type === 'separator') {
                                return <hr key={`sidetabs-${index}`} />;
                            }
                        } else {
                            return (
                                <button
                                    onClick={() => changeTab(index)}
                                    type="button"
                                    className={classNames('SideTabsNavItem', activeTab === index && 'Active')}
                                    key={`sidetabs-${index}`}
                                >
                                    {tab.number && <span className="SideTabsNavItemNumber">{tab.number}</span>}
                                    <span className="SideTabsNavItemTitle">{tab.title}</span>

                                    {tab.badge && <span className="SideTabsNavItemBadge">{tab.badge}</span>}
                                    {/*{tab.icons && tab.icons.length > 0 && (*/}
                                    {/*    <span className="SideTabsNavItemIcons">*/}
                                    {/*        {tab.icons.map((icon, key) => (*/}
                                    {/*            <span key={key} className="SideTabsNavItemIcon">*/}
                                    {/*                <Tooltip title={icon.title}>*/}
                                    {/*                    <Icon type={icon.icon} key={`sidetabs-icon-${index}`}*/}
                                    {/*                          width="12px" height="12px" />*/}
                                    {/*                </Tooltip>*/}
                                    {/*            </span>*/}
                                    {/*        ))}*/}
                                    {/*    </span>*/}
                                    {/*)}*/}
                                </button>
                            );
                        }
                    })}
                </ScrollShadow>
            </div>
            <div className="SideTabsContent">{tabs[activeTab].content}</div>
        </div>
    );
};

export default SideTabs;
