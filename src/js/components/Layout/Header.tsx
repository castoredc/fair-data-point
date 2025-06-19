import React, { Component } from 'react';
import { localizedText } from '../../util';
import DocumentTitle from '../DocumentTitle';
import { Link } from 'react-router-dom';
import Breadcrumbs from '../Breadcrumbs';
import LoginModal from '../../modals/LoginModal';
import { BreadcrumbsType } from 'types/BreadcrumbType';
import { UserType } from 'types/UserType';
import { 
    AppBar,
    Box,
    Button,
    Container,
    IconButton,
    Stack,
    Toolbar,
    Tooltip,
    Typography
} from '@mui/material';
import AccountCircleIcon from '@mui/icons-material/AccountCircle';
import ArrowBackIcon from '@mui/icons-material/ArrowBack';
import DropdownButton from '../Button/DropdownButton';
import LogoutIcon from '@mui/icons-material/Logout';
import SettingsIcon from '@mui/icons-material/Settings';

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
                icon: <SettingsIcon fontSize="small" />,
                label: 'Dashboard',
            },
            {
                destination: '/logout',
                icon: <LogoutIcon fontSize="small" />,
                label: 'Log out',
            },
        ];

        const menuItems = user && user.isAdmin ? [...adminMenuItems, ...defaultMenuItems] : defaultMenuItems;

        return (
            <Box component="header">
                <LoginModal
                    show={showModal}
                    handleClose={this.closeModal}
                    server={loginModalServer}
                    view={loginModalView}
                    path={encodeURIComponent(loginModalUrl ? loginModalUrl : window.location.pathname)}
                />
                {title && <DocumentTitle title={title} />}
                {!embedded && (
                    <Box>
                        <AppBar 
                            position="static" 
                            sx={{
                                height: mobile ? 56 : forceSmallHeader || smallHeader ? 64 : 80,
                                bgcolor: 'primary.main'
                            }}
                        >
                            <Toolbar sx={{ height: '100%' }}>
                                <Container>
                                    <Stack direction="row" sx={{ justifyContent: 'space-between', alignItems: 'center', height: '100%' }}>
                                        {mobile && breadcrumbs?.previous && (
                                            <Box>
                                                <Link
                                                    to={{
                                                        pathname: breadcrumbs.previous.path,
                                                        state: breadcrumbs.previous.state,
                                                    }}
                                                    style={{ textDecoration: 'none' }}
                                                >
                                                    <Tooltip title={`Go back to ${localizedText(breadcrumbs.previous.title, 'en')}`}>
                                                        <IconButton color="inherit">
                                                            <ArrowBackIcon />
                                                        </IconButton>
                                                    </Tooltip>
                                                </Link>
                                            </Box>
                                        )}
                                        <Box>
                                            <Link to="/fdp" style={{ textDecoration: 'none', color: 'inherit' }}>
                                                Logo
                                            </Link>
                                        </Box>
                                        <Box>
                                            {user ? (
                                                <Box>
                                                    <DropdownButton
                                                        text={user?.details?.fullName || 'User Menu'}
                                                        items={menuItems}
                                                        icon="account"
                                                        buttonType="primary"
                                                    />
                                                </Box>
                                            ) : mobile ? (
                                                <IconButton
                                                    color="inherit"
                                                    href={'/login?path=' + encodeURIComponent(window.location.pathname)}
                                                    onClick={this.openModal}
                                                >
                                                    <AccountCircleIcon />
                                                </IconButton>
                                            ) : (
                                                <Button
                                                    variant="outlined"
                                                    color="inherit"
                                                    href={'/login?path=' + encodeURIComponent(window.location.pathname)}
                                                    startIcon={<AccountCircleIcon />}
                                                    onClick={this.openModal}
                                                >
                                                    Log in
                                                </Button>
                                            )}
                                        </Box>
                                    </Stack>
                                </Container>
                            </Toolbar>
                        </AppBar>
                        {!mobile && breadcrumbs && <Breadcrumbs breadcrumbs={breadcrumbs.crumbs} />}
                    </Box>
                )}
                {!hideTitle && (
                    <Box sx={{ mt: 5, mb: 2 }}>
                        <Container>
                            {badge && (
                                <Box sx={{ mb: 1 }}>
                                    <Typography 
                                        component="span"
                                        sx={{
                                            px: 1,
                                            py: 0.5,
                                            borderRadius: 1,
                                            bgcolor: 'grey.100',
                                            color: 'text.secondary',
                                            fontSize: '0.875rem'
                                        }}
                                    >
                                        {badge}
                                    </Typography>
                                </Box>
                            )}
                            <Typography 
                                variant="h3" 
                                component="h1"
                                sx={{
                                    color: 'text.primary',
                                    fontSize: '2.1rem',
                                    lineHeight: 1.33,
                                    fontWeight: 500,
                                    maxWidth: '64rem'
                                }}
                            >
                                {title}
                            </Typography>
                        </Container>
                    </Box>
                )}
            </Box>
        );
    }
}

export default Header;
