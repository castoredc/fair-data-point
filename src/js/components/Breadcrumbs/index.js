import React, {Component} from 'react'

import './Breadcrumbs.scss'
import Container from "react-bootstrap/Container";

class Breadcrumbs extends Component {
    render() {
        const { children} = this.props;

        return <div className="Breadcrumbs">
            <Container>
                {children}
            </Container>
        </div>;
    }
}

export default Breadcrumbs