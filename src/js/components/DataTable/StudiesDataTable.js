import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {DataTable, Pagination} from "@castoredc/matter";
import {MethodType, StudyType} from "../MetadataItem/EnumMappings";
import Filters from "../Filters";
import {classNames} from "../../util";
import './DataTable.scss';

export default class StudiesDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStudies: true,
            hasLoadedStudies: false,
            studies:          [],
            pagination:       {
                currentPage:  1,
                start:        1,
                perPage:      25,
                totalResults: null,
                totalPages:   null,
            },
            isLoadingFilters: true,
            hasLoadedFilters: false,
            filterOptions:    [],
            appliedFilters:   {},
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getStudies();
        this.getFilters();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {lastHandledStudy} = this.props;

        if (lastHandledStudy !== prevProps.lastHandledStudy) {
            this.getStudies();
            this.getFilters();
        }
    }

    getStudies = () => {
        const {appliedFilters, pagination, hasLoadedStudies} = this.state;
        const {catalog, hideCatalog} = this.props;

        this.setState({
            isLoadingStudies: true,
        });

        let filters = appliedFilters;
        filters['page'] = pagination.currentPage;
        filters['perPage'] = pagination.perPage;

        if (hideCatalog) {
            filters['hideCatalogs'] = [hideCatalog.id];
        }

        if (hasLoadedStudies) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get(catalog ? '/api/catalog/' + catalog.slug + '/study' : '/api/study', {params: filters})
            .then((response) => {
                this.setState({
                    studies:          response.data.results,
                    pagination:       {
                        currentPage:  response.data.currentPage,
                        perPage:      response.data.perPage,
                        start:        response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages:   response.data.totalPages,
                    },
                    isLoadingStudies: false,
                    hasLoadedStudies: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudies: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the studies';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getFilters = () => {
        const {catalog} = this.props;

        this.setState({
            isLoadingStudies: true,
        });

        axios.get(catalog ? '/api/catalog/' + catalog.slug + '/study/filters' : '/api/study/filters')
            .then((response) => {
                this.setState({
                    filterOptions:    response.data,
                    isLoadingFilters: false,
                    hasLoadedFilters: true,
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
        const {pagination} = this.state;

        this.setState({
            appliedFilters: filters,
            pagination:     {
                ...pagination,
                currentPage: 1,
            },
        }, () => {
            this.getStudies();
        });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage:     paginationCount.pageLimit,
            },
        }, () => {
            this.getStudies();
        });
    };

    handleClick = (event, rowID, index) => {
        const {studies} = this.state;
        const {history, onClick} = this.props;

        if (typeof index !== "undefined" && studies.length > 0) {
            const study = studies.find((item) => item.id === rowID);

            if (onClick) {
                onClick(study);
            } else {
                history.push(`/admin/study/${study.id}`)
            }
        }
    };

    render() {
        const {studies, isLoadingStudies, hasLoadedStudies, hasLoadedFilters, filterOptions, pagination} = this.state;

        const hasLoaded = (hasLoadedStudies && hasLoadedFilters);

        if (!hasLoaded) {
            return <InlineLoader/>;
        }

        const rows = new Map(studies.map((item) => {
            return [
                item.id,
                {
                    cells: [
                        item.hasMetadata ? item.metadata.briefName : item.name,
                        item.hasMetadata ? StudyType[item.metadata.studyType] : null,
                        item.hasMetadata ? MethodType[item.metadata.methodType] : null,
                        item.hasMetadata && item.consent.publish ? {
                            type: 'tickSmall',
                        } : undefined,
                        item.hasMetadata && item.consent.socialMedia ? {
                            type: 'tickSmall',
                        } : undefined,
                        item.published ? {
                            type: 'view',
                        } : undefined,
                    ],
                },
            ];
        }));

        return <div className="DataTableContainer">
            <div className="TableCol">
                <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingStudies && 'Loading')}
                     ref={this.tableRef}>
                    <div className="DataTableWrapper">
                        <DataTable
                            emptyTableMessage="No studies found"
                            highlightRowOnHover
                            cellSpacing="default"
                            onClick={this.handleClick}
                            rows={rows}
                            structure={{
                                title:          {
                                    header:    'Name',
                                    resizable: true,
                                    template:  'text',
                                },
                                type:           {
                                    header:    'Type',
                                    resizable: true,
                                    template:  'text',
                                },
                                method:         {
                                    header:    'Method',
                                    resizable: true,
                                    template:  'text',
                                },
                                consentPublish: {
                                    header:   'Publish consent',
                                    icon:     'globe',
                                    template: 'icon',
                                },
                                consentSocial:  {
                                    header:   'Social consent',
                                    icon:     'share',
                                    template: 'icon',
                                },
                                published:      {
                                    header:   'Published',
                                    icon:     'view',
                                    template: 'icon',
                                },
                            }}
                        />
                    </div>

                    <Pagination
                        accessibleName="Pagination"
                        onChange={this.handlePagination}
                        pageLimit={pagination.perPage}
                        start={pagination.start}
                        totalItems={pagination.totalResults}
                    />

                </div>
            </div>
            <div className="Filters FilterCol">
                <Filters filters={filterOptions}
                         onFilter={(filter) => this.handleFilter(filter)}
                />
            </div>
        </div>;
    }
}