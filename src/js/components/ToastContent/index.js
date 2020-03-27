import React, {Component} from 'react';
import './ToastContent.scss'
// import ErrorIcon from '@material-ui/icons/Error';
// import WarningIcon from '@material-ui/icons/Warning';
// import InfoIcon from '@material-ui/icons/Info';
// import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import {classNames} from "../../util";

class ToastContent extends Component {
    render() {
        const { type, icon, message } = this.props;

        // let DisplayIcon = InfoIcon;
        //
        // if(typeof icon !== "undefined") {
        //     DisplayIcon = icon;
        // }
        // else if(type === 'error')
        // {
        //     DisplayIcon = ErrorIcon;
        // }
        // else if(type === 'warning')
        // {
        //     DisplayIcon = WarningIcon;
        // }
        // else if(type === 'success')
        // {
        //     DisplayIcon = CheckCircleIcon;
        // }
        // else if(type === 'info')
        // {
        //     DisplayIcon = InfoIcon;
        // }

        return (
            <div className={classNames('ToastContent', type)}>
                <div className="ToastContentIcon">
                    {/*<DisplayIcon />*/}
                </div>
                <div className="ToastContentMessage">
                    {message}
                </div>
            </div>
        );
    }
}

export default ToastContent;
