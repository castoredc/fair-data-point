import React, {Component} from 'react'

import './Breadcrumbs.scss'
import Breadcrumb from "./Breadcrumb";
import {localizedText} from "../../util";

class Breadcrumbs extends Component {
    render() {
        const { breadcrumbs } = this.props;

        return <div className="Breadcrumbs">
            <div className="container">
                {breadcrumbs.map((crumb) => {
                    return <Breadcrumb key={crumb.type} to={{
                        pathname: crumb.path,
                        state: crumb.state
                    }}>
                        {localizedText(crumb.title, 'en')}
                    </Breadcrumb>
                })}
            </div>
        </div>;
    }
}

export default Breadcrumbs