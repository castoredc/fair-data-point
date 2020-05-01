import React, {Component} from "react";
import {Route, Switch} from "react-router-dom";
import {Container} from "react-bootstrap";
import Logo from "../../../components/Logo";
import './PageWrapper.scss';
import NotFound from "../../NotFound";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import Navbar from "react-bootstrap/Navbar";
import Nav from "react-bootstrap/Nav";
import NavDropdown from "react-bootstrap/NavDropdown";
import Catalog from "../Catalog";
import Home from "../Home";
import Dataset from "../Dataset";
import Distribution from "../Distribution";

export default class PageWrapper extends Component {
    render() {
        const {user} = this.props;

        return <div className="Admin">
            <div className="Header">
                <Container>
                    <Row>
                        <Col className="LogoContainer">
                            <Logo />
                        </Col>
                        <Col className="MenuContainer">
                            <Navbar bg="transparent" variant="dark" expand="lg">
                                <Navbar.Toggle aria-controls="basic-navbar-nav" />
                                <Navbar.Collapse id="basic-navbar-nav">
                                    <Nav className="mr-auto">
                                    </Nav>
                                    <Nav>
                                        <NavDropdown title={user.fullName} id="basic-nav-dropdown" alignRight>
                                            <NavDropdown.Item href="/logout">Logout</NavDropdown.Item>
                                        </NavDropdown>
                                    </Nav>
                                </Navbar.Collapse>
                            </Navbar>
                        </Col>
                    </Row>
                </Container>
            </div>
            <div className="Main">
                <Container className="MainContainer">
                    <Switch>
                        <Route path="/admin" exact component={Home} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distribution/:distribution" component={Distribution} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset" component={Dataset} />
                        <Route path="/admin/catalog/:catalog" component={Catalog} />
                        <Route path="/admin/study/:study" component={Catalog} />
                        <Route component={NotFound} />
                    </Switch>
                </Container>
            </div>
        </div>;
    }
}
