import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {DataTable, Pagination} from "@castoredc/matter";
import {classNames, localizedText} from "../../util";

export default class DatasetsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDatasets: true,
            hasLoadedDatasets: false,
            datasets:          [],
            pagination:        {
                currentPage:  1,
                start:        1,
                perPage:      25,
                totalResults: null,
                totalPages:   null,
            },
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
            page:    pagination.currentPage,
            perPage: pagination.perPage,
        };

        if (hideCatalog) {
            filters['hideCatalogs'] = [hideCatalog.id];
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
                    datasets:          response.data.results,
                    pagination:        {
                        currentPage:  response.data.currentPage,
                        perPage:      response.data.perPage,
                        start:        response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages:   response.data.totalPages,
                    },
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
                perPage:     paginationCount.pageLimit,
            },
        }, () => {
            this.getDatasets();
        });
    };

    handleClick = (event, rowID, index) => {
        const {datasets} = this.state;
        const {catalog, history, onClick} = this.props;

        if (typeof index !== "undefined" && datasets.length > 0) {
            const dataset = datasets.find((item) => item.id === rowID);

            if (onClick) {
                onClick(dataset);
            } else {
                history.push({
                    pathname: '/admin/dataset/' + dataset.slug,
                    state:    {catalog: catalog},
                });
            }
        }
    };

    render() {
        const {datasets, isLoadingDatasets, hasLoadedDatasets, pagination} = this.state;

        if (!hasLoadedDatasets) {
            return <InlineLoader/>;
        }

        const rows = new Map(datasets.map((item) => {
            return [
                item.id,
                {
                    cells: [
                        item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)',
                        item.hasMetadata ? localizedText(item.metadata.description, 'en') : '',
                        item.hasMetadata ? item.metadata.language : '',
                        item.hasMetadata ? item.metadata.license : '',
                        item.published ? {
                            type: 'view',
                        } : undefined,
                    ],
                }];
        }));

        return <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingDatasets && 'Loading')}
                    ref={this.tableRef}>
            <div className="DataTableWrapper">
                <DataTable
                    emptyTableMessage="No datasets found"
                    highlightRowOnHover
                    cellSpacing="default"
                    onClick={this.handleClick}
                    rows={rows}
                    structure={{
                        title:       {
                            header:    'Title',
                            resizable: true,
                            template:  'text',
                        },
                        description: {
                            header:    'Description',
                            resizable: true,
                            template:  'text',
                        },
                        language:    {
                            header:    'Language',
                            resizable: true,
                            template:  'text',
                        },
                        license:     {
                            header:    'License',
                            resizable: true,
                            template:  'text',
                        },
                        published:   {
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

        </div>;
    }
}