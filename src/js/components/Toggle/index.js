import React, {Component} from 'react'
import Icon from '../Icon';
import {classNames} from "../../util";
import './Toggle.scss';

export default class Toggle extends Component {
    constructor(props) {
        super(props);
        this.state = {
            expanded: false
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
                    <Icon type="arrowDown" width={12} height={12} />
                </div>
            </div>
            {expanded && <div className={classNames('ToggleContent', expanded && 'Active')}>
                {children}
            </div>}
        </div>;
    }
}