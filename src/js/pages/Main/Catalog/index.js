import React, {Component} from "react";
import axios from "axios";

import {Col, Row} from "react-bootstrap";
import {classNames, localizedText} from "../../../util";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import StudyList from "../../../components/List/StudyList";
import {Button, Heading, ButtonGroup} from "@castoredc/matter";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";

export default class Catalog extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingFDP:       true,
            hasLoadedFDP:       false,
            isLoadingCatalog:   true,
            hasLoadedCatalog:   false,
            fdp:                null,
            catalog:            null,
            displayList:        true,
        };

        this.datasetsRef = React.createRef();
    }

    componentDidMount() {
        window.addEventListener("resize", this.resize.bind(this));
        this.resize();

        this.getFDP();
        this.getCatalog();
    }

    resize() {
        const { mobile, displayFilter } = this.state;

        const isMobile = window.innerWidth <= 767;

        this.setState({
            displayFilter: isMobile ? (mobile ? displayFilter : false) : true,
            mobile: isMobile
        });
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
        const { fdp, catalog, displayList, displayFilter, isLoadingFDP, isLoadingCatalog, mobile } = this.state;
        const { user, embedded, location } = this.props;

        const breadcrumbs = getBreadCrumbs(location, {fdp, catalog});

        const title = catalog ? localizedText(catalog.metadata.title, 'en') : null;

        const showFilterButton = mobile ? true : ! displayList;

        return <Layout
            className="Catalog"
            title={title}
            isLoading={(isLoadingFDP || isLoadingCatalog)}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={(isLoadingFDP || isLoadingCatalog)}>
                {(catalog && catalog.metadata.description && !embedded) && <Row>
                    <div className="MainCol">
                        <div className="InformationDescription">
                            {localizedText(catalog.metadata.description, 'en', true)}
                        </div>
                    </div>
                </Row>}

                <Row ref={this.datasetsRef}>
                    <div className={classNames('DatasetsHeader', 'MainCol', ! displayList && 'FullWidth')}>
                        <Row>
                            <Col className="DatasetsHeaderTitle">
                                <Heading type="Subsection">Studies</Heading>
                            </Col>
                            <Col className="DatasetHeaderButtons">
                                <ButtonGroup className="ToggleButton">
                                    <Button buttonType={displayList ? 'primary' : 'secondary'} onClick={() => this.changeView(true)} active={displayList}>List</Button>
                                    <Button buttonType={! displayList ? 'primary' : 'secondary'} onClick={() => this.changeView(false)} active={!displayList}>Map</Button>
                                </ButtonGroup>
                                <div className={classNames('FilterButton', ! showFilterButton && 'Hidden')}>
                                    <Button icon="filters" onClick={this.toggleFilter} isDropdown isOpen={displayFilter}>
                                        Filters
                                    </Button>
                                </div>
                            </Col>
                        </Row>
                    </div>
                </Row>

                <StudyList
                    state={breadcrumbs.current ? breadcrumbs.current.state : null}
                    catalog={catalog}
                    embedded={embedded}
                    displayList={displayList}
                    displayFilter={displayFilter}
                />
            </MainBody>
        </Layout>;
    }
}