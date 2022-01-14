import React from "react";
import {withRouter} from "react-router-dom";
import {classNames} from "../../util";

class Layout extends React.Component {
    componentDidUpdate(prevProps) {
        if (
            this.props.location.pathname !== prevProps.location.pathname
        ) {
            window.scrollTo(0, 0);
        }
    }

    render() {
        const {children, className, embedded, fullWidth} = this.props;

        return <div className={classNames('MainApp', className, embedded && 'Embedded', fullWidth && 'FullWidthApp')}>
            {children}
        </div>;
    }
}

export default withRouter(Layout);
