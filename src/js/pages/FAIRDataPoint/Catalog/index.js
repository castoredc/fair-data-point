import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import StudyListItem from "../../../components/ListItem/StudyListItem";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalog:   true,
            hasLoadedCatalog:   false,
            isLoadingDatasets:  true,
            hasLoadedDatasets:  false,
            catalog:            null,
            datasets:           []
        };
    }

    componentDidMount() {
        this.getCatalog();
        this.getDatasets();
    }

    getCatalog = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog: response.data,
                    isLoadingCatalog: false,
                    hasLoadedCatalog: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalog: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the catalog information';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getDatasets = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset')
            .then((response) => {
                this.setState({
                    datasets: response.data,
                    isLoadingDatasets: false,
                    hasLoadedDatasets: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDatasets: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the datasets';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if (this.state.isLoadingCatalog || this.state.isLoadingDatasets) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="Catalog"
            title={localizedText(this.state.catalog.title, 'en')}
            version={this.state.catalog.version}
            issued={this.state.catalog.issued}
            modified={this.state.catalog.modified}
            license={this.state.catalog.license}
        >
            <Row>
                <Col>
                    {(this.state.catalog.description && !embedded) && <div
                        className="InformationDescription">{localizedText(this.state.catalog.description, 'en', true)}</div>}

                    {/*<h2>Datasets</h2>*/}
                    {/*<div className="Description">*/}
                    {/*    Datasets are published collections of data.*/}
                    {/*</div>*/}
                    {this.state.datasets.length > 0 ? this.state.datasets.map((item, index) => {
                            return <StudyListItem key={index}
                                                  newWindow={embedded}
                                                  link={item.relative_url}
                                                  logo={item.logo}
                                                  name={localizedText(item.title, 'en')}
                                                  description={localizedText(item.shortDescription, 'en')}
                                                  recruitmentStatus={item.recruitmentStatus}
                                                  intervention={item.intervention}
                                                  condition={item.condition}
                            />
                        },
                    ) : <div className="NoResults">No datasets found.</div>}
                </Col>
                {/*<Col md={4}>*/}
                {/*    {this.state.catalog.language && <MetadataItem label="Language" url={this.state.catalog.language.url}*/}
                {/*                                                  value={this.state.catalog.language.name}/>}*/}
                {/*    {this.state.catalog.homepage &&*/}
                {/*    <MetadataItem label="Homepage" value={this.state.catalog.homepage}/>}*/}
                {/*</Col>*/}
            </Row>
        </FAIRDataInformation>;

        {/*{this.state.catalog.publishers.length > 0 && <div className="Publishers">*/
        }
        {/*    {this.state.catalog.publishers.map((item, index) => {*/
        }
        {/*            return <Contact key={index}*/
        }
        {/*                            url={item.url}*/
        }
        {/*                            type={item.type}*/
        }
        {/*                            name={item.name}/>*/
        }
        {/*        }*/
        }
        {/*    )}*/
        }
        {/*</div>}*/
        }
    }
}