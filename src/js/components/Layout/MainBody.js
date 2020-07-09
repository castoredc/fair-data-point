import React, {Component} from 'react';
import {Container} from "react-bootstrap";
import '../../pages/Main/Main.scss';
import LoadingScreen from "../LoadingScreen";
import InlineLoader from "../LoadingScreen/InlineLoader";

export default class MainBody extends Component {
    render() {
        const {children, isLoading} = this.props;

        if(isLoading) {
            return <InlineLoader />;
        }

        return <main>
            <Container>
                {children}
            </Container>
        </main>;
    }
}