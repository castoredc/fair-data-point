import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import Alert from "../../../components/Alert";
import queryString from "query-string";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

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
        const { location } = this.props;

        if(isLoadingDistribution)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        let restricted = false;

        if(distribution.accessRights === 2 || distribution.accessRights === 3)
        {
            restricted = true;
        }

        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        return <FAIRDataInformation
            embedded={embedded}
            className="Distribution"
            title={localizedText(distribution.metadata.title, 'en')}
            version={distribution.metadata.version.metadata}
            issued={distribution.metadata.issued}
            modified={distribution.metadata.modified}
            license={distribution.metadata.license}
            breadcrumbs={{...location.state, distribution: distribution}}
        >
            <Row>
                <Col md={8} className="InformationCol">
                    {distribution.metadata.description && <div className="InformationDescription">{localizedText(distribution.metadata.description, 'en', true)}</div>}

                    {restricted && <Alert
                        variant="info"
                        icon="lock">
                        The access to this distribution is restricted. When you try to access the data, you will be redirected to Castor EDC to authenticate yourself.
                    </Alert>
                    }
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
                <Col md={4}>
                    {/*{distribution.metadata.language && <MetadataItem label="Language" url={distribution.metadata.language.url} value={distribution.metadata.language.name} />}*/}
                </Col>
            </Row>
        </FAIRDataInformation>;
    }
}
