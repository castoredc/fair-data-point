import React, {Component, createRef} from 'react';
import {classNames, localizedText} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Container, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import '../../pages/Main/Main.scss';
import Breadcrumbs from "../Breadcrumbs";
import Col from "react-bootstrap/Col";
import './Header.scss';
import {Button, CastorLogo, Heading, Menu} from "@castoredc/matter";
import Nav from "react-bootstrap/Nav";

export default class Header extends Component {
    constructor(props) {
        super(props);

        this.state = {
            mobile: null,
            smallHeader: false
        };
    };

    componentDidMount() {
        window.addEventListener("resize", this.resize.bind(this));
        window.addEventListener("scroll", this.scroll.bind(this));
        this.resize();
    }

    componentWillUnmount(){
        window.removeEventListener('resize', this.resize.bind(this));
        window.removeEventListener('scroll', this.scroll.bind(this));
    }

    resize() {
        this.setState({mobile: window.innerWidth <= 767});
    }

    scroll() {
        const distanceY = window.pageYOffset || document.documentElement.scrollTop;
        const shrinkOn = 100;

        this.setState({smallHeader: distanceY > shrinkOn});
    }

    toggleMenu = () => {
        const { showMenu } = this.state;

        this.setState({
            showMenu: !showMenu
        });
    };

    render() {
        const { embedded, className, title, badge, location, data, breadcrumbs, user } = this.props;
        const { mobile, smallHeader, showMenu } = this.state;

        const menu = <div className="DropdownMenu">
            <Menu
                items={[
                    (user && user.isAdmin) && {
                        destination: '/admin',
                        icon: 'settings',
                        label: 'Admin'
                    },
                    {
                        destination: '/logout',
                        icon: 'logOut',
                        label: 'Log out'
                    },
                ]}
            />
        </div>;

        return <header className={classNames(className, embedded && 'Embedded', mobile ? 'Mobile' : 'Desktop')}>
            {title && <DocumentTitle title={title}/>}
            {!embedded && <div className="Header">
                <div className="Spacing" />
                {! mobile && <div className={classNames('MainHeader', smallHeader && 'Small')}>
                    <Container>
                        <Row>
                            <Col md={4} className="HeaderLogoCol">
                                <Link to="/fdp">
                                    <CastorLogo className="Logo" />
                                </Link>
                            </Col>
                            <Col md={8} className="HeaderUserCol">
                                {user ? <div>
                                    <Button icon="account" onClick={this.toggleMenu} isDropdown isOpen={showMenu}>
                                        {user.fullName}
                                    </Button>

                                    {showMenu && menu}

                                </div> : <Link to={'/login?path=' + encodeURIComponent(window.location.pathname)}>
                                    <Button icon="account">Log in</Button>
                                </Link>}
                            </Col>
                        </Row>
                    </Container>
                </div>}
                {(! mobile && breadcrumbs) && <Breadcrumbs breadcrumbs={breadcrumbs.crumbs} />}
                {mobile && <div className="MobileHeader">
                    <Container>
                        <Row>
                            <Col className="HeaderBackCol">
                                {(breadcrumbs && breadcrumbs.previous) && <Link to={{
                                    pathname: breadcrumbs.previous.path,
                                    state: breadcrumbs.previous.state
                                }}><Button
                                    icon="arrowLeft"
                                    iconDescription={`Go back to ${localizedText(breadcrumbs.previous.title, 'en')}`}
                                /></Link>}
                            </Col>
                            <Col className="HeaderLogoCol">
                                <Link to="/fdp">
                                    <CastorLogo className="Logo" />
                                </Link>
                            </Col>
                            <Col className="HeaderUserCol">
                                {user ? <div>
                                    <Button icon="account" iconDescription={user.fullName} onClick={this.toggleMenu}/>
                                    {showMenu && menu}
                                </div> : <Link to={'/login?path=' + encodeURIComponent(window.location.pathname)}>
                                    <Button icon="account" iconDescription="Log in" />
                                </Link>}
                            </Col>
                        </Row>
                    </Container>
                </div>}
            </div>}
            <div className="InformationHeader">
                <Container className="Children">
                    <Row>
                        <div className="MainCol">
                            {badge && <div><span className="InformationBadge">{badge}</span></div>}
                            <h1>
                                {title}
                            </h1>
                        </div>
                    </Row>
                </Container>
            </div>
        </header>;
    }
}