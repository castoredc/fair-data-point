import React, {Component} from "react";
import axios from "axios";

import {Col, Row} from "react-bootstrap";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import Alert from "../../../components/Alert";
import Header from "../../../components/Layout/Header";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import Layout from "../../../components/Layout";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";

export default class Distribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistribution:  true,
            hasLoadedDistribution:  false,
            distribution:           null
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
                    hasLoadedDistribution: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistribution: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { distribution, isLoadingDistribution } = this.state;
        const { location, user, embedded } = this.props;

        const breadcrumbs = getBreadCrumbs(location, {distribution});

        const restricted = distribution && (distribution.accessRights === 2 || distribution.accessRights === 3);
        const title = distribution ? localizedText(distribution.metadata.title, 'en') : null;

        return <Layout
            className="Distribution"
            title={title}
            isLoading={isLoadingDistribution}
            embedded={embedded}
        >
            <Header user={user} breadcrumbs={breadcrumbs} title={title} />

            <MainBody>
                {distribution && <Row>
                    <Col md={8} className="InformationCol">
                        {distribution.metadata.description && <div className="InformationDescription">{localizedText(distribution.metadata.description, 'en', true)}</div>}

                        {restricted && <Alert
                            variant="info"
                            icon="lock">
                            The access to this distribution is restricted. When you try to access the data, you will be redirected to Castor EDC to authenticate yourself.
                        </Alert>
                        }
                        {distribution.isCached && <ListItem link={distribution.relativeUrl + '/query'}
                                                            title="Query the data"
                                                            description="Use SPARQL queries to extract specific information from this distribution."
                                                            smallIcon={restricted && 'lock'}
                                                            newWindow
                        />}

                        {distribution.accessUrl && <ListItem link={distribution.accessUrl}
                                  title="Access the data"
                                  description="Get access to the distribution."
                                  smallIcon={restricted && 'lock'}
                                  newWindow
                        />}

                        {distribution.downloadUrl && <ListItem link={distribution.downloadUrl}
                                                               title="Download the data"
                                                               description="Get a downloadable file for this distribution."
                                                               smallIcon={restricted && 'lock'}
                                                               newWindow
                        />}
                    </Col>
                    <Col md={4} />
                </Row>}
            </MainBody>
        </Layout>;
    }
}
