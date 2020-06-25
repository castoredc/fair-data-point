import React, {Component} from 'react';
import {Container} from "react-bootstrap";
import '../../pages/Main/Main.scss';

export default class MainBody extends Component {
    render() {
        const {children} = this.props;
        return <main>
            <Container>
                {children}
            </Container>
        </main>;
    }
}