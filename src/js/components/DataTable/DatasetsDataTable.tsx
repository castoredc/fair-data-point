import React, { Component, RefObject } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import { localizedText } from '../../util';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef, GridRowParams } from '@mui/x-data-grid';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import VisibilityIcon from '@mui/icons-material/Visibility';
import VisibilityOffIcon from '@mui/icons-material/VisibilityOff';

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

interface DatasetsDataTableProps extends ComponentWithNotifications {
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

class DatasetsDataTable extends Component<DatasetsDataTableProps, DatasetsDataTableState> {
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
        const { catalog, study, hideCatalog, notifications } = this.props;

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
                notifications.show(message, { variant: 'error' });
            });
    };

    handlePagination = (currentPage: number, pageSize: number ) => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: currentPage + 1,
                    perPage: pageSize,
                },
            },
            () => {
                this.getDatasets();
            },
        );
    };

    handleClick = (params: GridRowParams) => {
        const { datasets } = this.state;
        const { catalog, history, onClick } = this.props;

        const dataset = datasets[params.id];

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

        const columns: GridColDef[] = [
            {
                headerName: 'Title',
                field: 'title',
            },
            {
                headerName: 'Description',
                field: 'description',
            },
            {
                headerName: 'Language',
                field: 'language',
            },
            {
                headerName: 'License',
                field: 'license',
            },
            {
                headerName: 'Published',
                field: 'published',
                resizable: false,
                width: 32,
                renderCell: (params) => {
                    return params.row.published ? <VisibilityIcon /> : <VisibilityOffIcon />;
                },
            },
        ];

        const rows = datasets.map((item, index) => {
            return {
                title: item.hasMetadata ? localizedText(item.metadata?.title, 'en') : '(no title)',
                description: item.hasMetadata ? localizedText(item.metadata?.description, 'en') : '',
                language: item.hasMetadata ? item.metadata?.language : '',
                license: item.hasMetadata ? item.metadata?.license : '',
                published: item.published,
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
                <DataGrid
                    disableRowSelectionOnClick
                    accessibleName="Datasets"
                    emptyStateContent="No datasets found"
                    onRowClick={this.handleClick}
                    rows={rows}
                    columns={columns}
                />
            </DataGridContainer>
        );
    }
}

export default withNotifications(DatasetsDataTable);