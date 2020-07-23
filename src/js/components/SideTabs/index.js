import React, {Component} from 'react'
import {classNames} from "../../util";
import './SideTabs.scss';
import {Icon} from "@castoredc/matter";

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
        const { tabs, hasButtons = false, hasTabs = false } = this.props;
        const { activeTab } = this.state;

        return <div className={classNames('SideTabs', hasButtons && 'HasButtons', hasTabs && 'HasTabs')}>
            <div className="SideTabsNav">
                {tabs.map((tab, index) => {
                    if(typeof tab.type !== 'undefined')
                    {
                        if(tab.type === 'separator') {
                            return <hr key={`sidetabs-${index}`}/>;
                        }
                    }
                    else {
                        return <button
                            onClick={() => this.changeTab(index)}
                            className={classNames('SideTabsNavItem', activeTab === index && 'Active')}
                            key={`sidetabs-${index}`}>
                            {tab.number && <span className="SideTabsNavItemNumber">{tab.number}</span>}
                            {tab.title}

                            {tab.badge && <span className="SideTabsNavItemBadge">{tab.badge}</span>}
                            {(tab.icons && tab.icons.length > 0) && <span className="SideTabsNavItemIcons">
                                {tab.icons.map((icon, key) => <Icon type={icon} key={`sidetabs-icon-${index}`} width="12px" height="12px" />)}
                            </span>}
                        </button>
                    }
                })}
            </div>
            <div className="SideTabsContent">
                {tabs[activeTab].content}
            </div>
        </div>;
    }
}