import React from "react";
import {withRouter} from "react-router-dom";
import {classNames} from "../../util";
import LoadingScreen from "../LoadingScreen";

class Layout extends React.Component {
    componentDidUpdate(prevProps) {
        if (
            this.props.location.pathname !== prevProps.location.pathname
        ) {
            window.scrollTo(0, 0);
        }
    }

    render() {
        const { children, className, isLoading, embedded } = this.props;

        if(isLoading) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <div className={classNames('MainApp', className, embedded && 'Embedded')}>
            {children}
        </div>;
    }
}

export default withRouter(Layout);
