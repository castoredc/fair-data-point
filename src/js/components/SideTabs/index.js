import React, {Component} from 'react'
import {classNames} from "../../util";
import './SideTabs.scss';

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
                            return <hr />;
                        }
                    }
                    else {
                        return <button
                            onClick={() => this.changeTab(index)}
                            className={classNames('SideTabsNavItem', activeTab === index && 'Active')}
                            key={`sidetabs-${index}`}>
                            {tab.title}

                            {tab.badge && <span className="SideTabsNavItemBadge">{tab.badge}</span>}
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