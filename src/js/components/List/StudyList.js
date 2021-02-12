import React, {Component} from "react";
import axios from "axios";
import {classNames} from "../../util";
import StudyListItem from "../ListItem/StudyListItem";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import StudiesMap from "../Map/StudiesMap";
import {Pagination} from "@castoredc/matter";
import InlineLoader from "../LoadingScreen/InlineLoader";
import DataGridHelper from "../DataTable/DataGridHelper";

export default class StudyList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingStudies: true,
            isLoadingMap: true,
            showMap: false,
            studies: null,
            map: null,
            pagination: {
                currentPage: 1,
                start: 1,
                perPage: props.embedded ? 5 : 10,
                totalResults: null,
                totalPages: null
            },
        };

        this.wrapperRef = React.createRef();
    }

    componentDidMount() {
        this.getStudies();
        this.getMap();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {appliedFilters} = this.props;

        if (prevProps.appliedFilters !== appliedFilters) {
            this.handleFilter();
        }
    }

    handleFilter = () => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: 1
            }
        }, () => {
            this.getStudies();
            this.getMap();
            window.scrollTo(0, this.wrapperRef.current.offsetTop + 50);
        });
    };


    getStudies = () => {
        const {pagination} = this.state;
        const {catalog, appliedFilters} = this.props;

        this.setState({
            isLoadingStudies: true,
        });

        let filters = appliedFilters;
        filters['page'] = pagination.currentPage;
        filters['perPage'] = pagination.perPage;

        axios.get('/api/catalog/' + catalog.slug + '/study', {params: filters})
            .then((response) => {
                const studies = response.data.results.filter((study) => {
                    return study.hasMetadata
                });

                this.setState({
                    studies: studies,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingStudies: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudies: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the studies';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getMap = () => {
        const {catalog, appliedFilters} = this.props;

        this.setState({
            isLoadingMap: true
        });

        axios.get('/api/catalog/' + catalog.slug + '/map', {params: appliedFilters})
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
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit
            }
        }, () => {
            this.getStudies();
            window.scrollTo(0, this.wrapperRef.current.offsetTop + 50);
        });
    };

    render() {
        const {isLoadingStudies, isLoadingMap, pagination, studies, map} = this.state;
        const {embedded, visible, representation, state, catalog} = this.props;

        if (!visible) {
            return null;
        }

        if (studies === null || map === null) {
            return <InlineLoader/>;
        }

        return <div className="Studies HasFilters" ref={this.wrapperRef}>
            {representation === 'list' &&
            <div className={classNames('LargeList', isLoadingStudies && 'Loading')}>
                {studies.length > 0 ? <div>
                    {studies.map((item, index) => {
                        if (item.hasMetadata === false) {
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

            {representation === 'map' && <div className={classNames('Map', isLoadingMap && 'Loading')}>
                <StudiesMap studies={map}/>
            </div>}
        </div>;
    }
}