import React, { Component } from 'react';
import { classNames, localizedText } from '../../util';
import DocumentTitle from '../DocumentTitle';
import { Link } from 'react-router-dom';
import '../../pages/Main/Main.scss';
import Breadcrumbs from '../Breadcrumbs';
import './Header.scss';
import Button from '@mui/material/Button';
import LoginModal from '../../modals/LoginModal';
import { BreadcrumbsType } from 'types/BreadcrumbType';
import { UserType } from 'types/UserType';
import Stack from '@mui/material/Stack';
import AccountCircleIcon from '@mui/icons-material/AccountCircle';
import { Container, IconButton } from '@mui/material';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import Tooltip from '@mui/material/Tooltip';
import Typography from '@mui/material/Typography';

interface HeaderProps {
    embedded?: boolean;
    className?: string;
    title?: string;
    badge?: string;
    breadcrumbs?: BreadcrumbsType; // Adjust based on breadcrumb structure
    user?: UserType | null;
    hideTitle?: boolean;
    forceSmallHeader?: boolean;
    showLoginModal?: boolean;
    loginModalUrl?: string;
    loginModalView?: string;
    loginModalServer?: string;
    onModalClose?: () => void;
}

interface HeaderState {
    mobile: boolean | null;
    smallHeader: boolean;
    showModal: boolean;
    loginModalUrl?: string;
    loginModalView?: string;
    loginModalServer?: string;
}

class Header extends Component<HeaderProps, HeaderState> {
    constructor(props) {
        super(props);

        this.state = {
            mobile: null,
            smallHeader: false,
            showModal: false,
            loginModalUrl: undefined,
            loginModalView: undefined,
            loginModalServer: undefined,
        };
    }

    componentDidMount() {
        window.addEventListener('resize', this.resize.bind(this));
        window.addEventListener('scroll', this.scroll.bind(this));
        this.resize();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { showLoginModal, loginModalUrl, loginModalView, loginModalServer } = this.props;
        const { showModal } = this.state;

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
        this.setState({ mobile: window.innerWidth <= 767 });
    }

    scroll() {
        const distanceY = window.pageYOffset || document.documentElement.scrollTop;
        const shrinkOn = 100;

        this.setState({ smallHeader: distanceY > shrinkOn });
    }

    openModal = e => {
        e.preventDefault();

        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        const {
            onModalClose = () => {
            },
        } = this.props;
        this.setState(
            {
                showModal: false,
            },
            () => {
                onModalClose();
            },
        );
    };

    render() {
        const {
            embedded,
            className,
            title,
            badge,
            breadcrumbs,
            user,
            hideTitle = false,
            forceSmallHeader = false,
        } = this.props;
        const { mobile, smallHeader, showModal, loginModalUrl, loginModalServer, loginModalView } = this.state;

        const adminMenuItems = [];

        const defaultMenuItems = [
            {
                destination: '/dashboard',
                icon: 'settings',
                label: 'Dashboard',
            },
            {
                destination: '/logout',
                icon: 'logOut',
                label: 'Log out',
            },
        ];

        const menuItems = user && user.isAdmin ? [...adminMenuItems, ...defaultMenuItems] : defaultMenuItems;

        return (
            <header className={classNames(className, embedded && 'Embedded', mobile ? 'Mobile' : 'Desktop')}>
                <LoginModal
                    show={showModal}
                    handleClose={this.closeModal}
                    server={loginModalServer}
                    view={loginModalView}
                    path={encodeURIComponent(loginModalUrl ? loginModalUrl : window.location.pathname)}
                />
                {title && <DocumentTitle title={title} />}
                {!embedded && (
                    <div className="Header">
                        <div className={classNames('Spacing', forceSmallHeader && 'Small')} />
                        {!mobile && (
                            <div
                                className={classNames('MainHeader', smallHeader && 'Small', forceSmallHeader && 'Small')}
                            >
                                <Container>
                                    <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                        <div className="HeaderLogoCol">
                                            <Link to="/fdp">
                                                Logo
                                            </Link>
                                        </div>
                                        <div className="HeaderUserCol">
                                            {user ? (
                                                <div>
                                                    {/*<DropdownButton*/}
                                                    {/*    text={user.details ? user.details.fullName : ''}*/}
                                                    {/*    items={menuItems}*/}
                                                    {/*    icon="account"*/}
                                                    {/*    buttonType="primary"*/}
                                                    {/*/>*/}
                                                </div>
                                            ) : (
                                                <Button
                                                    target="_blank"
                                                    href={'/login?path=' + encodeURIComponent(window.location.pathname)}
                                                    startIcon={<AccountCircleIcon />}
                                                    onClick={this.openModal}
                                                >
                                                    Log in
                                                </Button>
                                            )}
                                        </div>
                                    </Stack>
                                </Container>
                            </div>
                        )}
                        {!mobile && breadcrumbs && <Breadcrumbs breadcrumbs={breadcrumbs.crumbs} />}
                        {mobile && (
                            <div className="MobileHeader">
                                <Container>
                                    <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                        <div className="HeaderBackCol">
                                            {breadcrumbs && breadcrumbs.previous && (
                                                <Link
                                                    to={{
                                                        pathname: breadcrumbs.previous.path,
                                                        state: breadcrumbs.previous.state,
                                                    }}
                                                >
                                                    <Tooltip
                                                        title={`Go back to ${localizedText(breadcrumbs.previous.title, 'en')}`}>
                                                        <IconButton>
                                                            <ArrowBackIcon />
                                                        </IconButton>
                                                    </Tooltip>
                                                </Link>
                                            )}
                                        </div>
                                        <div className="HeaderLogoCol">
                                            <Link to="/fdp">
                                                Logo
                                            </Link>
                                        </div>
                                        <div className="HeaderUserCol">
                                            {user ? (
                                                <div>
                                                    {/*<DropdownButton*/}
                                                    {/*    iconDescription={user.details ? user.details.fullName : ''}*/}
                                                    {/*    items={menuItems}*/}
                                                    {/*    icon="account"*/}
                                                    {/*    buttonType="primary"*/}
                                                    {/*    hideDropdown={true}*/}
                                                    {/*/>*/}
                                                </div>
                                            ) : (
                                                <IconButton
                                                    target="_blank"
                                                    href={'/login?path=' + encodeURIComponent(window.location.pathname)}
                                                    onClick={this.openModal}
                                                >
                                                    <AccountCircleIcon />
                                                </IconButton>
                                            )}
                                        </div>
                                    </Stack>
                                </Container>
                            </div>
                        )}
                    </div>
                )}
                {!hideTitle && (
                    <div className="InformationHeader">
                        <Container>
                            {badge && (
                                <div>
                                    <span className="InformationBadge">{badge}</span>
                                </div>
                            )}
                            <Typography variant="h3" component="h1">{title}</Typography>
                        </Container>
                    </div>
                )}
            </header>
        );
    }
}

export default Header;
