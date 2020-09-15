import React, {Component} from 'react';
import {classNames, localizedText} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Link} from "react-router-dom";
import '../../pages/Main/Main.scss';
import Breadcrumbs from "../Breadcrumbs";
import './Header.scss';
import {Button, CastorLogo, Menu, Stack} from "@castoredc/matter";
import LoginModal from "../../modals/LoginModal";

export default class Header extends Component {
    constructor(props) {
        super(props);

        this.state = {
            mobile:        null,
            smallHeader:   false,
            showModal:     false,
            loginModalUrl: null,
            loginModalView: null,
            loginModalServer:        null,
        };
    };

    componentDidMount() {
        window.addEventListener("resize", this.resize.bind(this));
        window.addEventListener("scroll", this.scroll.bind(this));
        this.resize();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {showLoginModal, loginModalUrl, loginModalView, loginModalServer} = this.props;
        const {showModal} = this.state;

        if (showModal !== showLoginModal && showLoginModal !== prevProps.showLoginModal) {
            this.setState({
                showModal: !!showLoginModal,
            });
        }

        if (loginModalUrl !== prevProps.loginModalUrl) {
            this.setState({
                loginModalUrl: loginModalUrl,
            });
        }

        if (loginModalView !== prevProps.loginModalView) {
            this.setState({
                loginModalView: loginModalView,
            });
        }

        if (loginModalServer !== prevProps.loginModalServer) {
            this.setState({
                loginModalServer: loginModalServer,
            });
        }
    }

    componentWillUnmount() {
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
        const {showMenu} = this.state;

        this.setState({
            showMenu: !showMenu,
        });
    };

    openModal = (e) => {
        e.preventDefault();

        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        const {onModalClose = () => {}} = this.props;
        this.setState({
            showModal: false,
        }, () => {
            onModalClose();
        });
    };

    render() {
        const {embedded, className, title, badge, location, data, breadcrumbs, user} = this.props;
        const {mobile, smallHeader, showMenu, showModal, loginModalUrl, loginModalServer, loginModalView} = this.state;

        const menu = <div className="DropdownMenu">
            <Menu
                items={[
                    (user && user.isAdmin) && {
                        destination: '/admin',
                        icon:        'settings',
                        label:       'Admin',
                    },
                    {
                        destination: '/logout',
                        icon:        'logOut',
                        label:       'Log out',
                    },
                ]}
            />
        </div>;

        return <header className={classNames(className, embedded && 'Embedded', mobile ? 'Mobile' : 'Desktop')}>
            <LoginModal
                show={showModal}
                handleClose={this.closeModal}
                server={loginModalServer}
                view={loginModalView}
                path={encodeURIComponent(loginModalUrl ? loginModalUrl : window.location.pathname)}
            />
            {title && <DocumentTitle title={title}/>}
            {!embedded && <div className="Header">
                <div className="Spacing"/>
                {!mobile && <div className={classNames('MainHeader', smallHeader && 'Small')}>
                    <div className="container">
                        <Stack distribution="equalSpacing">
                            <div className="HeaderLogoCol">
                                <Link to="/fdp">
                                    <CastorLogo className="Logo"/>
                                </Link>
                            </div>
                            <div className="HeaderUserCol">
                                {user ? <div>
                                    <Button icon="account" onClick={this.toggleMenu} isDropdown isOpen={showMenu}>
                                        {user.details.fullName}
                                    </Button>

                                    {showMenu && menu}

                                </div> : <Button target="_blank"
                                                 href={'/login?path=' + encodeURIComponent(window.location.pathname)}
                                                 icon="account"
                                                 onClick={this.openModal}
                                >Log in
                                </Button>}
                            </div>
                        </Stack>
                    </div>
                </div>}
                {(!mobile && breadcrumbs) && <Breadcrumbs breadcrumbs={breadcrumbs.crumbs}/>}
                {mobile && <div className="MobileHeader">
                    <div className="container">
                        <Stack distribution="equalSpacing">
                            <div className="HeaderBackCol">
                                {(breadcrumbs && breadcrumbs.previous) && <Link to={{
                                    pathname: breadcrumbs.previous.path,
                                    state:    breadcrumbs.previous.state,
                                }}><Button
                                    icon="arrowLeft"
                                    iconDescription={`Go back to ${localizedText(breadcrumbs.previous.title, 'en')}`}
                                /></Link>}
                            </div>
                            <div className="HeaderLogoCol">
                                <Link to="/fdp">
                                    <CastorLogo className="Logo"/>
                                </Link>
                            </div>
                            <div className="HeaderUserCol">
                                {user ? <div>
                                    <Button icon="account" iconDescription={user.details.fullName} onClick={this.toggleMenu}/>
                                    {showMenu && menu}
                                </div> : <Button target="_blank"
                                                 href={'/login?path=' + encodeURIComponent(window.location.pathname)}
                                                 icon="account"
                                                 onClick={this.openModal}
                                                 iconDescription="Log in"/>}
                            </div>
                        </Stack>
                    </div>
                </div>}
            </div>}
            <div className="InformationHeader">
                <div className="container Children">
                    <div className="MainCol">
                        {badge && <div><span className="InformationBadge">{badge}</span></div>}
                        <h1>
                            {title}
                        </h1>
                    </div>
                </div>
            </div>
        </header>;
    }
}