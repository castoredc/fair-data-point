import React, {Component} from 'react'

import './Alert.scss'
import Icon from "../Icon";
import {classNames} from "../../util";

class Alert extends Component {
    render() {
        const { icon, message, variant} = this.props;

        return <div className={classNames('AlertMessage', 'AlertMessage-' + variant)}>
            {icon && <div className="AlertIcon">
                <Icon type={icon} />
            </div>}
            <div className="AlertText">
                {message}
            </div>
        </div>;
    }
}

export default Alert