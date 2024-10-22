import React, { Component, RefObject } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { CellText, DataGrid, LoadingOverlay } from '@castoredc/matter';
import moment from 'moment';
import DistributionGenerationStatus from '../Status/DistributionGenerationStatus';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import { apiClient } from 'src/js/network';

interface Log {
    createdAt: string;
    status: 'error' | 'success' | 'not_updated' | 'partially';
    records: {
        total: number;
    };
    id: string;
}

interface Pagination {
    currentPage: number;
    perPage: number;
}

interface DistributionLogsDataTableProps {
    dataset: string;
    distribution: { slug: string };
    study?: string;
    catalog?: string;
    history: {
        push: (location: { pathname: string }) => void;
    };
    lastHandledDataset?: any;
}

interface DistributionLogsDataTableState {
    isLoadingLogs: boolean;
    hasLoadedLogs: boolean;
    logs: Log[];
    pagination: Pagination;
}

export default class DistributionLogsDataTable extends Component<DistributionLogsDataTableProps, DistributionLogsDataTableState> {
    private tableRef: RefObject<HTMLDivElement>;

    constructor(props: DistributionLogsDataTableProps) {
        super(props);
        this.state = {
            isLoadingLogs: true,
            hasLoadedLogs: false,
            logs: [],
            pagination: DataGridHelper.getDefaultState(25),
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getLogs();
    }

    componentDidUpdate(prevProps: DistributionLogsDataTableProps) {
        const { lastHandledDataset } = this.props;

        if (lastHandledDataset !== prevProps.lastHandledDataset) {
            this.getLogs();
        }
    }

    getLogs = () => {
        const { pagination, hasLoadedLogs } = this.state;
        const { dataset, distribution } = this.props;

        this.setState({
            isLoadingLogs: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        if (hasLoadedLogs && this.tableRef.current) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        apiClient
            .get(`/api/dataset/${dataset}/distribution/${distribution.slug}/log`, { params: filters })
            .then(response => {
                this.setState({
                    logs: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingLogs: false,
                    hasLoadedLogs: true,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingLogs: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the logs';
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
                this.getLogs();
            }
        );
    };

    handleClick = (rowId: string) => {
        const { logs } = this.state;
        const { dataset, distribution, history, study, catalog } = this.props;

        const mainUrl = study ? `/dashboard/studies/${study}/datasets/${dataset}` : `/dashboard/catalogs/${catalog}/datasets/${dataset}`;

        const log = logs[rowId];

        history.push({
            pathname: mainUrl + '/distributions/' + distribution.slug + '/log/' + log.id,
        });
    };

    render() {
        const { logs, isLoadingLogs, hasLoadedLogs, pagination } = this.state;

        if (!hasLoadedLogs) {
            return <LoadingOverlay accessibleLabel="Loading logs" />;
        }

        const columns = [
            {
                Header: 'Date and time',
                accessor: 'createdAt',
            },
            {
                Header: 'Status',
                accessor: 'status',
            },
            {
                Header: 'Records',
                accessor: 'records',
            },
        ];

        const rows = logs.map((log, index) => ({
            createdAt: <CellText key={`createdAt-${index}`}>{moment(log.createdAt).format('DD-MM-YYYY HH:mm:ss')}</CellText>,
            status: (
                <CellText key={`status-${index}`}>
                    <DistributionGenerationStatus status={log.status} />
                </CellText>
            ),
            records: <CellText key={`records-${index}`}>{log.records.total}</CellText>,
        }));

        return (
            <DataGridContainer
                pagination={pagination}
                handlePageChange={this.handlePagination}
                fullHeight
                isLoading={isLoadingLogs}
                forwardRef={this.tableRef}
            >
                <DataGrid
                    accessibleName="Distribution logs"
                    emptyStateContent="No logs found"
                    onClick={this.handleClick}
                    rows={rows}
                    columns={columns}
                />
            </DataGridContainer>
        );
    }
}
