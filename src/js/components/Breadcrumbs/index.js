import React, {Component} from 'react'

import './Breadcrumbs.scss'
import Container from "react-bootstrap/Container";
import Breadcrumb from "./Breadcrumb";
import {localizedText} from "../../util";

class Breadcrumbs extends Component {
    render() {
        const { breadcrumbs } = this.props;

        const catalog = breadcrumbs.catalog || null;
        const study = breadcrumbs.study || null;
        const dataset = breadcrumbs.dataset || null;
        const distribution = breadcrumbs.distribution || null;
        const query = breadcrumbs.query || null;

        return <div className="Breadcrumbs">
            <Container>
                {breadcrumbs.map((crumb) => {
                    return <Breadcrumb key={crumb.type} to={{
                        pathname: crumb.path,
                        state: crumb.state
                    }}>
                        {localizedText(crumb.title, 'en')}
                    </Breadcrumb>
                })}
            </Container>
        </div>;
    }
}

export default Breadcrumbs