import React, {Component} from 'react'

import './Alert.scss'
import {classNames} from "../../util";
import {Icon} from "@castoredc/matter";

class Alert extends Component {
    render() {
        const { icon, children, variant, form} = this.props;

        return <div className={classNames('AlertMessage', 'AlertMessage-' + variant, form && 'AlertMessageForm')}>
            {icon && <div className="AlertIcon">
                <Icon type={icon} />
            </div>}
            <div className="AlertText">
                {children}
            </div>
        </div>;
    }
}

export default Alert