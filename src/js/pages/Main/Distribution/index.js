import React, {Component} from "react";
import axios from "axios";
import {classNames, localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import Header from "../../../components/Layout/Header";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Layout from "../../../components/Layout";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import MetadataSideBar from "../../../components/MetadataSideBar";
import './Distribution.scss';
import {isGranted} from "utils/PermissionHelper";
import {Banner} from "@castoredc/matter";
import {LockIcon} from "@castoredc/matter-icons";

export default class Distribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistribution: true,
            hasLoadedDistribution: false,
            distribution: null,
            showLoginModal: false,
            loginModalUrl: null,
            loginModalView: null,
            server: null,
        };
    }

    componentDidMount() {
        this.getDistribution();
    }

    getDistribution = () => {
        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution: response.data,
                    isLoadingDistribution: false,
                    hasLoadedDistribution: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistribution: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    showLoginModal = () => {
        const {distribution} = this.state;

        this.setState({
            showLoginModal: true,
            loginModalUrl: null,
            loginModalView: 'distribution',
            server: distribution.study.sourceServer,
        })
    };

    closeModal = () => {
        this.setState({
            showLoginModal: false,
            loginModalUrl: null,
            loginModalView: null,
            server: null,
        })
    };

    render() {
        const {distribution, isLoadingDistribution, showLoginModal, loginModalUrl, loginModalView, server} = this.state;
        const {history, location, user, embedded} = this.props;

        const breadcrumbs = getBreadCrumbs(location, {distribution});

        const restricted = distribution && (distribution.accessRights === 2 || distribution.accessRights === 3);
        const title = distribution ? localizedText(distribution.metadata.title, 'en') : null;

        return <Layout
            className="Distribution"
            title={title}
            isLoading={isLoadingDistribution}
            embedded={embedded}
        >
            <Header user={user} breadcrumbs={breadcrumbs} title={title} showLoginModal={showLoginModal}
                    loginModalUrl={loginModalUrl} onModalClose={this.closeModal} loginModalView={loginModalView}
                    loginModalServer={server}/>

            <MainBody isLoading={isLoadingDistribution}>
                {distribution && <>
                    <div className="MainCol">
                        {distribution.metadata.description && <div
                            className="InformationDescription">{localizedText(distribution.metadata.description, 'en', true)}</div>}
                    </div>

                    <div className="SideCol">
                        <MetadataSideBar type="distribution" metadata={distribution.metadata} name={title}/>
                    </div>

                    <hr className="Separator"/>

                    <div
                        className={classNames('MainCol DistributionAccess', !isGranted('access_data', distribution.permissions) && 'Restricted')}>
                        {(!user && !isGranted('access_data', distribution.permissions)) && <div className="Overlay">
                            <Banner
                                customIcon={<LockIcon/>}
                                title="The access to the data in this distribution is restricted"
                                description="In order to access the data, please log in with your Castor EDC account."
                                actions={[
                                    {
                                        label: 'Log in with Castor',
                                        onClick: () => window.location.href = '/connect/castor/' + distribution.study.sourceServer + '?target_path=' + distribution.relativeUrl
                                    }
                                ]}
                            />
                        </div>
                        }

                        {user && !isGranted('access_data', distribution.permissions) && <div className="Overlay">
                            <Banner
                                type="error"
                                customIcon={<LockIcon/>}
                                title="You do not have access to the data inside this distribution"
                                description="The access to the data is restricted and your account has not been granted access."
                            />
                        </div>
                        }

                        {isGranted('access_data', distribution.permissions) &&
                        <div className="DistributionAccessButtons">
                            {distribution.isCached && <ListItem link={distribution.relativeUrl + '/query'}
                                                                title="Query the data"
                                                                description="Use SPARQL queries to extract specific information from this distribution."
                                                                smallIcon={restricted && (isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock')}
                                                                newWindow
                            />}

                            {distribution.accessUrl && <ListItem link={distribution.accessUrl}
                                                                 title="Access the data"
                                                                 description="Get access to the distribution."
                                                                 smallIcon={restricted && (isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock')}
                                                                 newWindow
                            />}

                            {distribution.downloadUrl && <ListItem link={distribution.downloadUrl}
                                                                   title="Download the data"
                                                                   description="Get a downloadable file for this distribution."
                                                                   smallIcon={restricted && (isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock')}
                                                                   newWindow
                            />}
                        </div>}
                    </div>
                </>}
            </MainBody>
        </Layout>;
    }
}
