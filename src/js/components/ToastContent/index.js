import React, {Component} from 'react';
import './ToastContent.scss'
import {classNames} from "../../util";
import {Icon} from "@castoredc/matter";

class ToastContent extends Component {
    render() {
        const {type, icon, message} = this.props;

        let displayIcon = 'info';

        if (icon) {
            displayIcon = icon;
        } else if (type === 'success') {
            displayIcon = 'tickCircledInverted';
        } else if (type === 'error') {
            displayIcon = 'errorCircled';
        }

        return (
            <div className={classNames('ToastContent', type)}>
                <div className="ToastContentIcon">
                    <Icon type={displayIcon}/>
                </div>
                <div className="ToastContentMessage">
                    {message}
                </div>
            </div>
        );
    }
}

export default ToastContent;
