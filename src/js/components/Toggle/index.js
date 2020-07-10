import React, {Component} from 'react'
import {classNames} from "../../util";
import './Toggle.scss';
import {Icon} from "@castoredc/matter";

export default class Toggle extends Component {
    constructor(props) {
        super(props);

        this.state = {
            expanded: props.expanded ? props.expanded : false
        };
    }

    toggle = () => {
        const { expanded } = this.state;

        this.setState({
            expanded: !expanded
        });
    };

    render() {
        const { title, children } = this.props;
        const { expanded } = this.state;

        return <div className={classNames('Toggle', expanded && 'Active')}>
            <div className="ToggleHeader" onClick={this.toggle} tabIndex="0" role="button">
                {title}

                <div className={classNames('ToggleArrow', expanded && 'Active')}>
                    <Icon type="arrowBottom" width={12} height={12} />
                </div>
            </div>
            <div className={classNames('ToggleContent', expanded && 'Active')}>
                {children}
            </div>
        </div>;
    }
}