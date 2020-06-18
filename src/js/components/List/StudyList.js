import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {classNames} from "../../util";
import {Sticky, StickyContainer} from 'react-sticky';
import StudyListItem from "../ListItem/StudyListItem";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import Filters from "../Filters";
import DatasetMap from "../DatasetMap";
import {Pagination} from "@castoredc/matter";
import InlineLoader from "../LoadingScreen/InlineLoader";

export default class StudyList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingStudies:   true,
            isLoadingMap:       true,
            showMap:            false,
            studies:           null,
            map:                null,
            pagination:       {
                currentPage: 1,
                start: 0,
                perPage: props.embedded ? 5 : 10,
                totalResults: null,
                totalPages: null
            },
            isLoadingFilters: true,
            filterOptions:    [],
            appliedFilters:   {},
        };
    }

    componentDidMount() {
        this.getStudies();
        this.getMap();
        this.getFilters();
    }

    getStudies = () => {
        const { appliedFilters, pagination } = this.state;
        const { catalog } = this.props;

        this.setState({
            isLoadingStudies: true,
        });

        let filters = appliedFilters;
        filters['page'] = pagination.currentPage;
        filters['perPage'] = pagination.perPage;

        axios.get('/api/catalog/' + catalog.slug + '/study', { params: filters })
            .then((response) => {
                const studies = response.data.results.filter((study) => { return study.hasMetadata });

                this.setState({
                    studies: studies,
                    pagination:       {
                        currentPage: response.data.currentPage,
                        perPage: response.data.perPage,
                        start: response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages: response.data.totalPages
                    },
                    isLoadingStudies: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudies: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the studies';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getMap = () => {
        const { appliedFilters } = this.state;
        const { catalog } = this.props;

        this.setState({
            isLoadingMap: true
        });

        axios.get('/api/catalog/' + catalog.slug + '/map', { params: appliedFilters })
            .then((response) => {
                this.setState({
                    map: response.data,
                    isLoadingMap: false,
                    showMap: response.data.length > 0
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingMap: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the map';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getFilters = () => {
        const { catalog } = this.props;

        this.setState({
            isLoadingStudies: true,
        });

        axios.get('/api/catalog/' + catalog.slug + '/study/filters')
            .then((response) => {
                this.setState({
                    filterOptions:    response.data,
                    isLoadingFilters: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingFilters: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the filters';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handleFilter = (filters) => {
        const { pagination } = this.state;

        this.setState({
            appliedFilters: filters,
            pagination: {
                ...pagination,
                currentPage: 1
            }
        }, () => {
            this.getStudies();
        });
    };

    handlePagination = (paginationCount) => {
        const { pagination } = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit
            }
        }, () => {
            this.getStudies();
        });
    };

    render() {
        const { embedded, isLoadingStudies, isLoadingMap, isLoadingFilters, filterOptions, pagination, studies, map } = this.state;
        const { displayList, displayFilter, fdp, catalog } = this.props;

        const displayMap = ! displayList;
        const listWidth = displayList ? 8 : 12;

        if(studies === null || map === null)
        {
            return <InlineLoader />;
        }

        return <StickyContainer>
                <Row className="Datasets">
                    <Col md={listWidth} className="InformationCol">
                        {displayList && <div className={classNames('Datasets', isLoadingStudies && 'Loading')}>
                            {studies.length > 0 ? <div>
                                {studies.map((item, index) => {
                                    if(item.hasMetadata === false) {
                                        return null;
                                    }
                                    return <StudyListItem key={index}
                                                          newWindow={embedded}
                                                          fdp={fdp}
                                                          catalog={catalog}
                                                          link={`/study/${item.slug}`}
                                                          name={item.metadata.briefName}
                                                          description={item.metadata.briefSummary}
                                                          recruitmentStatus={item.metadata.recruitmentStatus}
                                                          intervention={item.metadata.intervention}
                                                          condition={item.metadata.condition}
                                    />
                                })}

                                <Pagination
                                    accessibleName="Pagination"
                                    onChange={this.handlePagination}
                                    pageLimit={pagination.perPage}
                                    start={pagination.start}
                                    totalItems={pagination.totalResults}
                                />

                            </div> : <div className="NoResults">No studies found.</div>}
                        </div>}

                        {displayMap && <div className={classNames('Map', isLoadingMap && 'Loading')}>
                            <DatasetMap datasets={map} />
                        </div>}
                    </Col>
                    <Col md={4}
                         className={classNames('Filters',
                             ! displayList && 'StickyDisabled',
                             ! displayFilter && 'Hidden',
                             (! displayList && displayFilter) && 'Overlay')}>
                        {studies.length > 0 && <Sticky>
                            {({style, isSticky}) => (
                                <Filters filters={filterOptions}
                                         isLoading={isLoadingFilters}
                                         style={style}
                                         sticky={isSticky}
                                         onFilter={(filter) => this.handleFilter(filter)}
                                 />
                            )}
                        </Sticky>}
                    </Col>
                </Row>
            </StickyContainer>;
    }
}