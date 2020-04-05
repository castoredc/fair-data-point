import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {classNames, localizedText} from "../../../util";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import {Sticky, StickyContainer} from 'react-sticky';
import StudyListItem from "../../../components/ListItem/StudyListItem";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Filters from "../../../components/Filters";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import Button from "react-bootstrap/Button";
import DatasetMap from "../../../components/DatasetMap";
import ButtonToolbar from "react-bootstrap/ButtonToolbar";
import Icon from "../../../components/Icon";

export default class Catalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFDP:       true,
            hasLoadedFDP:       false,
            isLoadingCatalog:   true,
            hasLoadedCatalog:   false,
            isLoadingDatasets:  true,
            hasLoadedDatasets:  false,
            isLoadingMap:       true,
            hasLoadedMap:       false,
            fdp:                null,
            catalog:            null,
            showDatasets:       false,
            showMap:            false,
            datasets:           [],
            map:                [],
            displayList:        true,
            displayFilter:      true
        };
    }

    componentDidMount() {
        this.setState({ displayFilter: (window.innerWidth > 767) });
        this.getFDP();
        this.getCatalog();
        this.getDatasets(false);
        this.getMap(false);
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

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

    getDatasets = (filters) => {
        this.setState({
            isLoadingDatasets: true
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset', { params: filters })
            .then((response) => {
                this.setState({
                    datasets: response.data,
                    isLoadingDatasets: false,
                    hasLoadedDatasets: true,
                    showDatasets: (filters === false && response.data.length > 0 || filters !== false)
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

    getMap = (filters) => {
        this.setState({
            isLoadingMap: true
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/map', { params: filters })
            .then((response) => {
                this.setState({
                    map: response.data,
                    isLoadingMap: false,
                    hasLoadedMap: true,
                    showMap: (filters === false && response.data.length > 0 || filters !== false)
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingMap: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the map';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    handleFilter = (filters) => {
        this.getDatasets(filters);
        this.getMap(filters);
    };

    changeView = (displayList) => {
        this.setState({
            displayList: displayList,
            displayFilter: displayList && (window.innerWidth > 767)
        });
    };

    toggleFilter = () => {
        this.setState({
            displayFilter: ! this.state.displayFilter
        });
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');
        const listWidth = this.state.displayList ? 8 : 12;
        const headerWidth = this.state.displayList ? 5 : 4;
        const buttonWidth = this.state.displayList ? 3 : 8;

        if (this.state.isLoadingFDP || this.state.isLoadingCatalog) {
            return <LoadingScreen showLoading={true}/>;
        }

        if (embedded && 'parentIFrame' in window){
            parentIFrame.size();
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="Catalog"
            title={localizedText(this.state.catalog.title, 'en')}
            version={this.state.catalog.version}
            issued={this.state.catalog.issued}
            modified={this.state.catalog.modified}
            license={this.state.catalog.license}
            back={{link: this.state.fdp.relative_url, text: localizedText(this.state.fdp.title, 'en')}}
        >
            {(this.state.catalog.description && !embedded) && <Row>
                <Col md={12} className="InformationCol">
                    <div className={classNames('InformationDescription', (this.state.showDatasets && 'HasChildren'))}>
                        {localizedText(this.state.catalog.description, 'en', true)}
                    </div>
                </Col>
            </Row>}
            {!this.state.showDatasets && (this.state.isLoadingDatasets) && <Row>
                <Col md={12}>
                    <InlineLoader />
                </Col>
            </Row>}
            {this.state.showDatasets && <div>
                <Row className="DatasetsHeader">
                    <Col sm={headerWidth} className="DatasetsHeaderTitle">
                        <h2>Studies</h2>
                    </Col>
                    <Col sm={buttonWidth} className="DatasetHeaderButtons">
                        {this.state.showMap && <ButtonGroup>
                            <Button variant="outline-primary" onClick={() => this.changeView(true)} active={this.state.displayList}>List</Button>
                            <Button variant="outline-primary" onClick={() => this.changeView(false)} active={! this.state.displayList}>Map</Button>
                        </ButtonGroup>}
                        <ButtonGroup className={classNames('FilterButton', this.state.displayList && 'Hidden')}>
                            <Button variant="outline-primary" onClick={this.toggleFilter} active={this.state.displayFilter}>
                                <Icon type="filters" /> Filters
                            </Button>
                        </ButtonGroup>
                    </Col>
                </Row>
            <StickyContainer>
                <Row className="Datasets">
                    <Col md={listWidth} className="InformationCol">
                        {(this.state.isLoadingDatasets && this.state.displayList || this.state.isLoadingMap && ! this.state.displayList) && <InlineLoader overlay={true} />}
                        {this.state.displayList ? <div className={classNames('Datasets', this.state.isLoadingDatasets && 'Loading')}>
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
                            }) : <div className="NoResults">No studies found.</div>}
                        </div> : <div className={classNames('Map', this.state.isLoadingMap && 'Loading')}>
                            <DatasetMap datasets={this.state.map} />
                        </div>}
                    </Col>
                    <Col md={4}
                         className={classNames('Filters',
                             !this.state.displayList && 'StickyDisabled',
                             ! this.state.displayFilter && 'Hidden',
                             (! this.state.displayList && this.state.displayFilter) && 'Overlay')}>
                        <Sticky>
                            {({style, isSticky}) => (
                                  <Filters className={classNames(isSticky && 'Sticky')}
                                           style={style}
                                           catalog={this.props.match.params.catalog}
                                           onFilter={(filter) => this.handleFilter(filter)}
                                  />
                            )}
                        </Sticky>
                    </Col>
                </Row>
            </StickyContainer>
            </div>}
        </FAIRDataInformation>;
    }
}