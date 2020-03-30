import React, {Component} from 'react';
import {classNames} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Container, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import Logo from '../Logo';
import './AdminPage.scss';

export default class AdminPage extends Component {
    render() {
        const {className, title, children} = this.props;
        return <div className={classNames(className, 'TopLevelContainer', 'AdminPageContainer')}>
            <DocumentTitle title={'Admin | ' + title}/>

            <div className="Header">
                <div className="MainHeader">
                    <Container>
                        <Link to="/admin">
                            <Logo />
                        </Link>
                    </Container>
                </div>
                <Container>
                    <div className="InformationHeader">
                        <div className="InformationHeaderTop">
                            <h1 className="Title">
                                {title}
                            </h1>
                        </div>
                    </div>
                </Container>
            </div>
            <div className="Information">
                <Row className="InformationRow">
                    <Container className="Children">
                        {children}
                    </Container>
                </Row>
            </div>
        </div>;
    }
}