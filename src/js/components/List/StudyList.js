import React, {Component} from "react";
import axios from "axios";
import {classNames} from "../../util";
import {Sticky, StickyContainer} from 'react-sticky';
import StudyListItem from "../ListItem/StudyListItem";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import Filters from "../Filters";
import StudiesMap from "../Map/StudiesMap";
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
                start: 1,
                perPage: props.embedded ? 5 : 10,
                totalResults: null,
                totalPages: null
            },
            isLoadingFilters: true,
            filterOptions:    [],
            appliedFilters:   {},
        };

        this.wrapperRef = React.createRef();
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
            window.scrollTo(0, this.wrapperRef.current.offsetTop - 130);
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
            window.scrollTo(0, this.wrapperRef.current.offsetTop - 130);
        });
    };

    render() {
        const { isLoadingStudies, isLoadingMap, isLoadingFilters, filterOptions, pagination, studies, map } = this.state;
        const { embedded, displayList, displayFilter, state, catalog } = this.props;

        const displayMap = ! displayList;

        if(studies === null || map === null)
        {
            return <InlineLoader />;
        }

        return <StickyContainer className="StickyContainer">
                <div className="Datasets HasFilters" ref={this.wrapperRef}>
                    <div className={classNames('MainCol', displayMap && 'FullWidth')}>
                        {displayList && <div className={classNames('Datasets', isLoadingStudies && 'Loading')}>
                            {studies.length > 0 ? <div>
                                {studies.map((item, index) => {
                                    if(item.hasMetadata === false) {
                                        return null;
                                    }
                                    return <StudyListItem key={index}
                                                          newWindow={embedded}
                                                          state={state}
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
                                    itemsPerPageOptions={[
                                        {
                                            label: '10',
                                            value: '10'
                                        },
                                        {
                                            label: '25',
                                            value: '25'
                                        },
                                        {
                                            label: '50',
                                            value: '50'
                                        }
                                    ]}
                                />

                            </div> : <div className="NoResults">No studies found.</div>}
                        </div>}

                        {displayMap && <div className={classNames('Map', isLoadingMap && 'Loading')}>
                            <StudiesMap studies={map} />
                        </div>}
                    </div>
                    <div
                         className={classNames('SideCol', 'Filters',
                             ! displayList && 'StickyDisabled',
                             ! displayFilter && 'Hidden',
                             (! displayList && displayFilter) && 'Overlay')}>
                        <Sticky>
                            {({style, isSticky}) => (
                                <Filters filters={filterOptions}
                                         isLoading={isLoadingFilters}
                                         style={style}
                                         sticky={isSticky}
                                         onFilter={(filter) => this.handleFilter(filter)}
                                 />
                            )}
                        </Sticky>
                    </div>
                </div>
            </StickyContainer>;
    }
}