import React, {Component} from "react";
import axios from "axios/index";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Col, Row} from "react-bootstrap";

export default class FAIRDataPointMain extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFDP: true,
            isLoadingCatalogs: true,
            hasLoadedFDP: false,
            hasLoadedCatalogs: false,
            fdp: null,
            catalogs: []
        };
    }

    componentDidMount() {
        this.getFDP();
        this.getCatalogs();
    }

    getFDP = () => {
        axios.get('/api/fdp')
            .then((response) => {
                this.setState({
                    fdp: response.data,
                    isLoadingFDP: false,
                    hasLoadedFDP: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingFDP: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getCatalogs = () => {
        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs: response.data,
                    isLoadingCatalogs: false,
                    hasLoadedCatalogs: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalogs: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { fdp, catalogs, isLoadingFDP, isLoadingCatalogs } = this.state;
        
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(isLoadingFDP || isLoadingCatalogs)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="FAIRDataPoint"
            title={localizedText(fdp.title, 'en')}
            version={fdp.version}
            license={fdp.license}
            breadcrumbs={{fdp: fdp}}
        >
            <Row>
                <Col className="InformationCol">
                    <h2>Collections</h2>
                    {catalogs.length > 0 ? catalogs.map((item, index) => {
                        if(item.hasMetadata === false) {
                            return null;
                        }
                        return <ListItem key={index}
                                         newWindow={embedded}
                                         link={item.relativeUrl}
                                         title={localizedText(item.metadata.title, 'en')}
                                         description={localizedText(item.metadata.description, 'en')} />
                    }) : <div className="NoResults">No catalogs found.</div>}
                </Col>
            </Row>
        </FAIRDataInformation>;

        // {fdp.publishers.length > 0 && <div className="Publishers">
        //     {fdp.publishers.map((item, index) => {
        //         return <Contact key={index}
        //                         url={item.url}
        //                         type={item.type}
        //                         name={item.name} />}
        //     )}
        // </div>}
    }
}
