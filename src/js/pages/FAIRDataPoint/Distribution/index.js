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
            isLoadingDataset:       true,
            hasLoadedDataset:       false,
            isLoadingDistribution:  true,
            hasLoadedDistribution:  false,
            dataset:                null,
            distribution:           null
        };
    }

    componentDidMount() {
        this.getDataset();
        this.getDistribution();
    }

    getDataset = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset)
            .then((response) => {
                this.setState({
                    dataset: response.data,
                    isLoadingDataset: false,
                    hasLoadedDataset: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataset: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the dataset';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getDistribution = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution)
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        if(this.state.isLoadingDataset || this.state.isLoadingDistribution)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        let restricted = false;

        if(this.state.distribution.accessRights === 2 || this.state.distribution.accessRights === 3)
        {
            restricted = true;
        }

        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        return <FAIRDataInformation
            embedded={embedded}
            className="Dataset"
            title={localizedText(this.state.distribution.title, 'en')}
            version={this.state.distribution.version}
            issued={this.state.distribution.issued}
            modified={this.state.distribution.modified}
            license={this.state.distribution.license}
        >
            <Row>
                <Col md={8} className="InformationCol">
                    {this.state.distribution.description && <div className="InformationDescription">{localizedText(this.state.distribution.description, 'en', true)}</div>}

                    {restricted && <Alert
                        variant="info"
                        icon="lock"
                        message="The access to this distribution is restricted. When you try to access the data, you will be redirected to Castor EDC to authenticate yourself."/>
                    }
                    <ListItem link={this.state.distribution.access_url}
                              title="Access the data"
                              description="Get access to the distribution."
                              smallIcon={restricted && 'lock'} />

                    <ListItem link={this.state.distribution.download_url}
                              title="Download the data"
                              description="Get a downloadable file for this distribution."
                              smallIcon={restricted && 'lock'} />
                </Col>
                <Col md={4}>
                    {this.state.dataset.logo && <div className="InformationLogo">
                        <img src={this.state.dataset.logo} alt={'Logo'}/>
                    </div>}
                    {/*{this.state.distribution.language && <MetadataItem label="Language" url={this.state.distribution.language.url} value={this.state.distribution.language.name} />}*/}
                </Col>
            </Row>
        </FAIRDataInformation>;

        {/*{this.state.distribution.publishers.length > 0 && <div className="Publishers">*/}
        {/*    {this.state.distribution.publishers.map((item, index) => {*/}
        {/*        return <Contact key={index}*/}
        {/*                        url={item.url}*/}
        {/*                        type={item.type}*/}
        {/*                        name={item.name} />}*/}
        {/*    )}*/}
        {/*</div>}*/}
    }
}
