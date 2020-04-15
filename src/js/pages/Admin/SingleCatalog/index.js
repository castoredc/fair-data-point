import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {classNames, localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import AdminPage from "../../../components/AdminPage";
import AdminStudyListItem from "../../../components/ListItem/AdminStudyListItem";
import Container from "react-bootstrap/Container";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Pagination from "react-bootstrap/Pagination";
import Filters from "../../../components/Filters";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import Icon from "../../../components/Icon";
import arrowLeft from "../../../components/Icon/icons/arrow-left.svg";

export default class SingleCatalog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingCatalog:  true,
            isLoadingDatasets: true,
            hasLoadedCatalogs: false,
            hasLoadedDatasets: false,
            catalog:           null,
            datasets:          [],
            filters:           {},
            perPage:           25,
            pages:             null,
            page:              null,
            displayFilter:     false,
        };

        this.datasetsRef = React.createRef();
    }

    componentDidMount() {
        this.getCatalog();
        this.getDatasets(false);
    }

    getCatalog = () => {
        this.setState({
            isLoadingCatalog: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog:          response.data,
                    isLoadingCatalog: false,
                    hasLoadedCatalog: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.message !== "undefined") {
                    this.setState({
                        isLoadingCatalog: false,
                        hasError:         true,
                        errorMessage:     error.response.data.message,
                    });
                } else {
                    this.setState({
                        isLoadingCatalog: false,
                    });
                }
            });
    };

    getDatasets = (filters) => {
        const {perPage} = this.state;

        let newFilters = filters;

        if (newFilters === false) {
            newFilters = {page: 1};
        } else {
            window.scrollTo(0, this.datasetsRef.current.offsetTop - 35);
        }

        newFilters['perPage'] = perPage;
        newFilters['admin'] = true;

        this.setState({
            isLoadingDatasets: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset', {params: newFilters})
            .then((response) => {
                this.setState({
                    datasets:          response.data.datasets,
                    perPage:           response.data.perPage,
                    pages:             response.data.pages,
                    page:              response.data.page,
                    isLoadingDatasets: false,
                    hasLoadedDatasets: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDatasets: false,
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the datasets';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handleFilter = (filters) => {
        this.setState({filters: filters});

        let newFilters = filters;
        newFilters['page'] = 1;

        this.getDatasets(newFilters);
    };

    changePage = (page) => {
        const {filters} = this.state;
        this.setState({page: page});

        let newFilters = filters;
        newFilters['page'] = page;

        this.getDatasets(newFilters);
    };

    getPagination = () => {
        const {page, pages} = this.state;

        let items = [];

        for (let number = 1; number <= pages; number++) {
            items.push(
                <Pagination.Item key={number} active={number === page} onClick={() => {
                    this.changePage(number)
                }}>
                    {number}
                </Pagination.Item>,
            );
        }

        return items;
    };

    toggleFilter = () => {
        this.setState({
            displayFilter: ! this.state.displayFilter
        });
    };

    render() {
        const {pages} = this.state;

        if (this.state.isLoadingCatalog) {
            return <LoadingScreen showLoading={true}/>;
        }

        if (!this.state.hasLoadedCatalog || !this.state.hasLoadedDatasets) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <AdminPage
            className="Catalog"
            title={localizedText(this.state.catalog.title, 'en')}
        >
            <Row>
                <Col sm={6}>
                    <div className="ButtonBar">
                        <LinkContainer to="/admin">
                            <Button variant="link" className="BackButton"><Icon type="arrowLeft" /> All catalogs</Button>
                        </LinkContainer>
                    </div>
                </Col>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <LinkContainer to={'/admin/' + this.props.match.params.catalog + '/study/add'}>
                            <Button variant="primary" className="AddButton"><Icon type="add" /> Add study</Button>
                        </LinkContainer>

                        <ButtonGroup className={classNames('FilterButton', this.state.displayList && 'Hidden')}>
                            <Button variant="outline-primary" onClick={this.toggleFilter} active={this.state.displayFilter}>
                                <Icon type="filters" /> Filters
                            </Button>
                        </ButtonGroup>
                    </div>
                </Col>
            </Row>
            <Row className="Datasets" ref={this.datasetsRef}>
                <Col md={12}>
                    {this.state.isLoadingDatasets ? <Row>
                        <Col md={12}>
                            <InlineLoader/>
                        </Col>
                    </Row> : <div>
                        {this.state.datasets.length > 0 ? <Container>
                            <Row className="ListItem AdminListItem AdminListItemHeader">
                                <Col md={7}>Name</Col>
                                <Col md={1}><small>Consent</small> Social</Col>
                                <Col md={1}><small>Consent</small> Publish</Col>
                                <Col md={1}>Published</Col>
                                <Col md={2} />
                            </Row>

                            {this.state.datasets.map((item, index) => {
                                    return <AdminStudyListItem key={index}
                                                               id={item.studyId}
                                                               catalog={this.props.match.params.catalog}
                                                               name={localizedText(item.title, 'en')}
                                                               slug={item.slug}
                                                               consent={item.consent}
                                    />
                                },
                            )}

                            {pages > 1 && <div className="Pagination">
                                <Pagination>
                                    {this.getPagination()}
                                </Pagination>
                            </div>}
                            </Container> : <div className="NoResults">No studies found.</div>}
                    </div>}
                </Col>
                <Col md={4} className={classNames('Filters Overlay',
                                                        ! this.state.displayFilter && 'Hidden')}>
                    <Filters catalog={this.props.match.params.catalog}
                             onFilter={(filter) => this.handleFilter(filter)}
                    />
                </Col>
            </Row>
        </AdminPage>;
    }
}