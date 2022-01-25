import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {CellText, DataGrid, Heading, Icon, IconCell, LoadingOverlay} from "@castoredc/matter";
import {MethodType, StudyType} from "../MetadataItem/EnumMappings";
import './DataTable.scss';
import DataGridHelper from "./DataGridHelper";
import DataGridContainer from "./DataGridContainer";
import StudyFilters from "../Filters/StudyFilters";

export default class StudiesDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStudies: true,
            hasLoadedStudies: false,
            studies: [],
            pagination: DataGridHelper.getDefaultState(25),
            isLoadingFilters: true,
            hasLoadedFilters: false,
            filterOptions: [],
            appliedFilters: {},
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
            filters['hideCatalogs'] = [hideCatalog];
        }

        if (hasLoadedStudies) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get(catalog ? '/api/catalog/' + catalog + '/study' : '/api/study', {params: filters})
            .then((response) => {
                this.setState({
                    studies: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
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

        axios.get(catalog ? '/api/catalog/' + catalog + '/study/filters' : '/api/study/filters')
            .then((response) => {
                this.setState({
                    filterOptions: response.data,
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
            pagination: {
                ...pagination,
                currentPage: 0,
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
                currentPage: paginationCount.currentPage + 1,
                perPage: paginationCount.pageLimit,
            },
        }, () => {
            this.getStudies();
        });
    };

    handleClick = (rowId) => {
        const {studies} = this.state;
        const {history, onClick} = this.props;

        const study = studies[rowId];

        if (onClick) {
            onClick(study);
        } else {
            history.push(`/dashboard/studies/${study.id}`)
        }
    };

    render() {
        const {studies, isLoadingStudies, hasLoadedStudies, hasLoadedFilters, filterOptions, pagination} = this.state;

        const hasLoaded = (hasLoadedStudies && hasLoadedFilters);

        if (!hasLoaded) {
            return <LoadingOverlay accessibleLabel="Loading studies"/>;
        }

        const columns = [
            {
                Header: 'Name',
                accessor: 'title',
            },
            {
                Header: 'Type',
                accessor: 'type',
            },
            {
                Header: 'Method',
                accessor: 'method',
            },
            {
                Header: <Icon description="Published" type="view"/>,
                accessor: 'published',
                disableResizing: true,
                width: 32
            },
        ];

        const rows = studies.map((item) => {
            return {
                title: <CellText>{item.hasMetadata ? item.metadata.briefName : item.name}</CellText>,
                type: <CellText>{item.hasMetadata ? StudyType[item.metadata.studyType] : null}</CellText>,
                method: <CellText>{item.hasMetadata ? MethodType[item.metadata.methodType] : null}</CellText>,
                published: item.published ? <IconCell icon={{type: 'view'}}/> : undefined,
            };
        });

        return <div className="DataTableContainer">
            <div className="TableCol">
                <DataGridContainer
                    pagination={pagination}
                    handlePageChange={this.handlePagination}
                    fullHeight
                    isLoading={isLoadingStudies}
                    ref={this.tableRef}
                >
                    <DataGrid
                        accessibleName="Studies"
                        emptyStateContent="No studies found"
                        onClick={this.handleClick}
                        rows={rows}
                        columns={columns}
                    />
                </DataGridContainer>
            </div>
            <div className="Filters FilterCol">
                <Heading type="Subsection">Search studies</Heading>

                <StudyFilters filters={filterOptions}
                              onFilter={(filter) => this.handleFilter(filter)}
                />
            </div>
        </div>;
    }
}