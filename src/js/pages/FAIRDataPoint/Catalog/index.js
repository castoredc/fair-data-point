import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {classNames, localizedText} from "../../../util";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import StudyList from "../../../components/List/StudyList";
import {Button, Stack} from "@castoredc/matter";

export default class Catalog extends Component {
    constructor(props) {
        super(props);

        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        this.state = {
            isLoadingFDP:       true,
            hasLoadedFDP:       false,
            isLoadingCatalog:   true,
            hasLoadedCatalog:   false,
            fdp:                null,
            catalog:            null,
            embedded:           embedded,
            displayList:        true,
        };

        this.datasetsRef = React.createRef();
    }

    componentDidMount() {
        this.setState({ displayFilter: (window.innerWidth > 767) });
        this.getFDP();
        this.getCatalog();
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

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalog information';
                toast.error(<ToastContent type="error" message={message} />);
            });
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
        const { catalog, embedded, displayList, displayFilter } = this.state;

        if (this.state.isLoadingFDP || this.state.isLoadingCatalog) {
            return <LoadingScreen showLoading={true}/>;
        }

        const headerWidth = displayList ? 5 : 4;
        const buttonWidth = displayList ? 3 : 8;

        return <FAIRDataInformation
            embedded={embedded}
            className="Catalog"
            title={localizedText(catalog.title, 'en')}
            version={catalog.version}
            issued={catalog.issued}
            modified={catalog.modified}
            license={catalog.license}
            back={{link: this.state.fdp.relativeUrl, text: localizedText(this.state.fdp.title, 'en')}}
        >
            {(catalog.description && !embedded) && <Row>
                <Col md={12} className="InformationCol">
                    <div className={classNames('InformationDescription', (this.state.showDatasets && 'HasChildren'))}>
                        {localizedText(catalog.description, 'en', true)}
                    </div>
                </Col>
            </Row>}

            <Row className="DatasetsHeader" ref={this.datasetsRef}>
                <Col md={headerWidth} className="DatasetsHeaderTitle">
                    <h2>Studies</h2>
                </Col>
                <Col md={buttonWidth} className="DatasetHeaderButtons">
                    <Stack className="ToggleButton">
                        <Button buttonType={displayList ? 'primary' : 'secondary'} onClick={() => this.changeView(true)} active={displayList}>List</Button>
                        <Button buttonType={! displayList ? 'primary' : 'secondary'} onClick={() => this.changeView(false)} active={!displayList}>Map</Button>
                    </Stack>
                    <div className={classNames('FilterButton', displayList && 'Hidden')}>
                        <Button icon="filters" onClick={this.toggleFilter} isDropdown isOpen={displayFilter}>
                            Filters
                        </Button>
                    </div>
                </Col>
            </Row>

            <StudyList
                catalog={catalog}
                embedded={embedded}
                displayList={displayList}
                displayFilter={displayFilter}
            />
        </FAIRDataInformation>;
    }
}