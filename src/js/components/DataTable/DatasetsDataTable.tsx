import React, { Component, RefObject } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { CellText, DataGrid, Icon, IconCell, LoadingOverlay } from '@castoredc/matter';
import { localizedText } from '../../util';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';

interface Dataset {
    hasMetadata?: boolean;
    metadata?: {
        title: string;
        description: string;
        language: string;
        license: string;
    };
    slug: string;
    published?: boolean;
}

interface Pagination {
    currentPage: number;
    perPage: number;
}

interface DatasetsDataTableProps {
    catalog?: string;
    study?: string;
    hideCatalog?: string;
    lastHandledDataset?: any;
    onClick?: (dataset: Dataset) => void;
    history?: {
        push: (location: { pathname: string; state: { catalog: string } }) => void;
    };
}

interface DatasetsDataTableState {
    isLoadingDatasets: boolean;
    hasLoadedDatasets: boolean;
    datasets: Dataset[];
    pagination: Pagination;
}

export default class DatasetsDataTable extends Component<DatasetsDataTableProps, DatasetsDataTableState> {
    private tableRef: RefObject<HTMLDivElement>;

    constructor(props: DatasetsDataTableProps) {
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

    componentDidUpdate(prevProps: DatasetsDataTableProps) {
        const { lastHandledDataset } = this.props;

        if (lastHandledDataset !== prevProps.lastHandledDataset) {
            this.getDatasets();
        }
    }

    getDatasets = () => {
        const { pagination, hasLoadedDatasets } = this.state;
        const { catalog, study, hideCatalog } = this.props;

        this.setState({
            isLoadingDatasets: true,
        });

        const filters: { page: number; perPage: number; hideParents?: string[] } = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        if (hideCatalog) {
            filters['hideParents'] = [hideCatalog];
        }

        if (hasLoadedDatasets && this.tableRef.current) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        let url = '/api/dataset';

        if (catalog) {
            url = '/api/catalog/' + catalog + '/dataset';
        }

        if (study) {
            url = '/api/study/' + study + '/dataset';
        }

        apiClient
            .get(url, { params: filters })
            .then(response => {
                this.setState({
                    datasets: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingDatasets: false,
                    hasLoadedDatasets: true,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingDatasets: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the datasets';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    handlePagination = (paginationCount: { currentPage: number; pageSize: number }) => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: paginationCount.currentPage + 1,
                    perPage: paginationCount.pageSize,
                },
            },
            () => {
                this.getDatasets();
            }
        );
    };

    handleClick = (rowId: string) => {
        const { datasets } = this.state;
        const { catalog, history, onClick } = this.props;

        const dataset = datasets[rowId];

        if (onClick) {
            onClick(dataset);
        } else if (history && catalog) {
            history.push({
                pathname: '/dashboard/catalogs/' + catalog + '/datasets/' + dataset.slug,
                state: { catalog: catalog },
            });
        }
    };

    render() {
        const { datasets, isLoadingDatasets, hasLoadedDatasets, pagination } = this.state;

        if (!hasLoadedDatasets) {
            return <LoadingOverlay accessibleLabel="Loading datasets" />;
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
                Header: <Icon description="Published" type="view" />,
                accessor: 'published',
                disableResizing: true,
                width: 32,
            },
        ];

        const rows = datasets.map((item, index) => {
            return {
                title: <CellText key={`title-${index}`}>{item.hasMetadata ? localizedText(item.metadata?.title, 'en') : '(no title)'}</CellText>,
                description: <CellText key={`desc-${index}`}>{item.hasMetadata ? localizedText(item.metadata?.description, 'en') : ''}</CellText>,
                language: <CellText key={`lang-${index}`}>{item.hasMetadata ? item.metadata?.language : ''}</CellText>,
                license: <CellText key={`lic-${index}`}>{item.hasMetadata ? item.metadata?.license : ''}</CellText>,
                published: item.published ? <IconCell key={`pub-${index}`} icon={{ type: 'view' }} /> : undefined,
            };
        });

        return (
            <DataGridContainer
                pagination={pagination}
                handlePageChange={this.handlePagination}
                fullHeight
                isLoading={isLoadingDatasets}
                forwardRef={this.tableRef}
            >
                <DataGrid accessibleName="Datasets" emptyStateContent="No datasets found" onClick={this.handleClick} rows={rows} columns={columns} />
            </DataGridContainer>
        );
    }
}
