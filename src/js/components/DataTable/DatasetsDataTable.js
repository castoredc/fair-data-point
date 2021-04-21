import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {CellText, DataGrid, Icon, IconCell} from "@castoredc/matter";
import {localizedText} from "../../util";
import DataGridHelper from "./DataGridHelper";
import DataGridContainer from "./DataGridContainer";

export default class DatasetsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDatasets: true,
            hasLoadedDatasets: false,
            datasets: [],
            pagination: DataGridHelper.getDefaultState(25),
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getDatasets();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {lastHandledDataset} = this.props;

        if (lastHandledDataset !== prevProps.lastHandledDataset) {
            this.getDatasets();
        }
    }

    getDatasets = () => {
        const {pagination, hasLoadedDatasets} = this.state;
        const {catalog, study, hideCatalog} = this.props;

        this.setState({
            isLoadingDatasets: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        if (hideCatalog) {
            filters['hideParents'] = [hideCatalog.id];
        }

        if (hasLoadedDatasets) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        let url = '/api/dataset';

        if (catalog) {
            url = '/api/catalog/' + catalog.slug + '/dataset';
        }

        if (study) {
            url = '/api/study/' + study.id + '/dataset';
        }

        axios.get(url, {params: filters})
            .then((response) => {
                this.setState({
                    datasets: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingDatasets: false,
                    hasLoadedDatasets: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDatasets: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the datasets';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit,
            },
        }, () => {
            this.getDatasets();
        });
    };

    handleClick = (rowId) => {
        const {datasets} = this.state;
        const {catalog, history, onClick} = this.props;

        const dataset = datasets[rowId];

        if (onClick) {
            onClick(dataset);
        } else {
            history.push({
                pathname: '/admin/dataset/' + dataset.slug,
                state: {catalog: catalog},
            });
        }
    };

    render() {
        const {datasets, isLoadingDatasets, hasLoadedDatasets, pagination} = this.state;

        if (!hasLoadedDatasets) {
            return <InlineLoader/>;
        }

        const columns = [
            {
                Header: 'Title',
                accessor: 'title',
            },
            {
                Header: 'Description',
                accessor: 'description',
            },
            {
                Header: 'Language',
                accessor: 'language',
            },
            {
                Header: 'License',
                accessor: 'license',
            },
            {
                Header: <Icon description="Published" type="view"/>,
                accessor: 'published',
                disableResizing: true,
                width: 32
            }
        ];

        const rows = datasets.map((item) => {
            return {
                title: <CellText>
                    {item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)'}
                </CellText>,
                description: <CellText>
                    {item.hasMetadata ? localizedText(item.metadata.description, 'en') : ''}
                </CellText>,
                language: <CellText>{item.hasMetadata ? item.metadata.language : ''}</CellText>,
                license: <CellText>{item.hasMetadata ? item.metadata.license : ''}</CellText>,
                published: item.published ? <IconCell icon={{type: 'view'}}/> : undefined,
            }
        });

        return <DataGridContainer
            pagination={pagination}
            handlePageChange={this.handlePagination}
            fullHeight
            isLoading={isLoadingDatasets}
            ref={this.tableRef}
        >
            <DataGrid
                accessibleName="Datasets"
                emptyStateContent="No datasets found"
                onClick={this.handleClick}
                rows={rows}
                columns={columns}
            />
        </DataGridContainer>;
    }
}