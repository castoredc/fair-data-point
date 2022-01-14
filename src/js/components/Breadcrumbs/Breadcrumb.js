import React, {Component} from 'react'

import './Breadcrumbs.scss'
import {Link} from "react-router-dom";

class Breadcrumb extends Component {
    render() {
        const {children, to} = this.props;

        return <div className="Breadcrumb">
            {to ? <Link to={to}>{children}</Link> : children}
        </div>;
    }
}

export default Breadcrumb