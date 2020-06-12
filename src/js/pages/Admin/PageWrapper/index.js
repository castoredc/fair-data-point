import React, {Component, createRef} from "react";
import {Redirect, Route, Switch} from "react-router-dom";
import {Container} from "react-bootstrap";
import Logo from "../../../components/Logo";
import './PageWrapper.scss';
import NotFound from "../../NotFound";
import Navbar from "react-bootstrap/Navbar";
import Nav from "react-bootstrap/Nav";
import Study from "../Study";
import DataModels from "../Home/DataModels";
import DataModel from "../DataModel";
import {LinkContainer} from 'react-router-bootstrap'
import Catalogs from "../Home/Catalogs";
import {Icon, Menu} from "@castoredc/matter";
import Studies from "../Home/Studies";
import Catalog from "../Catalog";
import Distribution from "../Distribution";
import Dataset from "../Dataset";

export default class PageWrapper extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showMenu: false
        };

        this.link = createRef();
        this.menu = createRef();
    };

    toggleMenu = () => {
        const { showMenu } = this.state;

        this.setState({
            showMenu: !showMenu
        });
    };

    render() {
        const {user} = this.props;
        const {showMenu} = this.state;

        return <div className="Admin">
            <div className="Header">
                <Container>
                    <Navbar bg="transparent" variant="dark" expand="lg">
                        <Navbar.Brand className="LogoContainer">
                            <Logo />
                        </Navbar.Brand>
                        <Navbar.Toggle aria-controls="basic-navbar-nav" />
                        <Navbar.Collapse id="basic-navbar-nav">
                            <Nav className="mr-auto">
                                <LinkContainer to={'/admin/catalogs'}>
                                    <Nav.Link>
                                        <Icon type="folderClose" /> Catalogs
                                    </Nav.Link>
                                </LinkContainer>
                                <LinkContainer to={'/admin/studies'}>
                                    <Nav.Link>
                                        <Icon type="study" /> Studies
                                    </Nav.Link>
                                </LinkContainer>
                                <LinkContainer to={'/admin/models'}>
                                    <Nav.Link>
                                        <Icon type="structure" /> Data models
                                    </Nav.Link>
                                </LinkContainer>
                            </Nav>
                            <Nav>
                                <Nav.Link onClick={this.toggleMenu} active={showMenu} ref={this.link}>
                                    <Icon type="account" className="AccountIcon" /> {user.fullName} <Icon type="arrowBottom" className="DropdownIcon" />
                                </Nav.Link>

                                {showMenu && <div className="DropdownMenu" ref={this.menu}>
                                    <Menu
                                        items={[
                                            {
                                                destination: '/logout',
                                                icon: 'logOut',
                                                label: 'Log out'
                                            }
                                        ]}
                                    />
                                </div>}
                            </Nav>
                        </Navbar.Collapse>
                    </Navbar>
                </Container>
            </div>
            <div className="Main">
                <Container className="MainContainer">
                    <Switch>
                        <Redirect exact from="/admin" to="/admin/catalog" />

                        <Route path="/admin/catalogs" exact component={Catalogs} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset/distribution/:distribution" component={Distribution} />
                        <Route path="/admin/catalog/:catalog/dataset/:dataset" component={Dataset} />
                        <Route path="/admin/catalog/:catalog" component={Catalog} />

                        <Route path="/admin/dataset/:dataset" component={Dataset} />
                        <Route path="/admin/dataset/:dataset/distribution/:distribution" component={Distribution} />

                        <Route path="/admin/studies" exact component={Studies} />
                        <Route path="/admin/study/:study" component={Study} />

                        <Route path="/admin/models" exact component={DataModels} />
                        <Route path="/admin/model/:model" component={DataModel} />
                        <Route component={NotFound} />
                    </Switch>
                </Container>
            </div>
        </div>;
    }
}
