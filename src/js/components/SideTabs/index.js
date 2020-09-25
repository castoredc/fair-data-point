import React, {Component} from 'react'
import {classNames} from "../../util";
import './SideTabs.scss';
import {Icon, Stack, Tooltip} from "@castoredc/matter";
import ScrollShadow from "../ScrollShadow";

export default class SideTabs extends Component {
    constructor(props) {
        super(props);

        this.state = {
            activeTab: 0
        };
    }

    changeTab = (index) => {
        this.setState({
            activeTab: index
        });
    };

    render() {
        const {tabs, hasButtons = false, hasTabs = false, title, actions} = this.props;
        const {activeTab} = this.state;

        return <div
            className={classNames('SideTabs', hasButtons && 'HasButtons', title && 'HasTitle', hasTabs && 'HasTabs')}>
            <div className="SideTabsNav">
                {(title || actions) && <div className="SideTabsHeader">
                    <Stack distribution="equalSpacing">
                        {title && <div className="SideTabsHeaderTitle">
                            {title}
                        </div>}

                        {actions && <div className="SideTabsHeaderActions">
                            {actions}
                        </div>}
                    </Stack>
                </div>}
                <ScrollShadow className="SideTabsScrollable">
                    {tabs.map((tab, index) => {
                        if (typeof tab.type !== 'undefined') {
                            if (tab.type === 'separator') {
                                return <hr key={`sidetabs-${index}`}/>;
                            }
                        } else {
                            return <button
                                onClick={() => this.changeTab(index)}
                                className={classNames('SideTabsNavItem', activeTab === index && 'Active')}
                                key={`sidetabs-${index}`}>
                                {tab.number && <span className="SideTabsNavItemNumber">{tab.number}</span>}
                                <span className="SideTabsNavItemTitle">{tab.title}</span>

                                {tab.badge && <span className="SideTabsNavItemBadge">{tab.badge}</span>}
                                {(tab.icons && tab.icons.length > 0) && <span className="SideTabsNavItemIcons">
                                    {tab.icons.map((icon, key) => <span className="SideTabsNavItemIcon"><Tooltip
                                        content={icon.title}
                                        hideOnBlur
                                    >
                                        <Icon type={icon.icon} key={`sidetabs-icon-${index}`} width="12px"
                                              height="12px"/>
                                    </Tooltip>
                                    </span>)}
                                </span>}
                            </button>
                        }
                    })}
                </ScrollShadow>
            </div>
            <div className="SideTabsContent">
                {tabs[activeTab].content}
            </div>
        </div>;
    }
}