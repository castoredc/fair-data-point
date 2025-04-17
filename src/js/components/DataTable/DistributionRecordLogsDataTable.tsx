import React, { Component, RefObject } from 'react';
import moment from 'moment/moment';
import DistributionGenerationStatus from '../Status/DistributionGenerationStatus';
import FormItem from '../Form/FormItem';
import DataGridHelper from './DataGridHelper';
import DataGridContainer from './DataGridContainer';
import Split from 'components/Layout/Split';
import { apiClient } from 'src/js/network';
import LoadingOverlay from 'components/LoadingOverlay';
import { Typography } from '@mui/material';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef, GridRowParams } from '@mui/x-data-grid';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';

interface Log {
    id: string;
    record: { id: string };
    status: 'error' | 'success' | 'not_updated' | 'partially';
    createdAt: string;
    errors?: { exception: string; message: string }[];
}

interface Pagination {
    currentPage: number;
    perPage: number;
}

interface DistributionRecordLogsDataTableProps extends ComponentWithNotifications {
    dataset: string;
    distribution: { slug: string };
    log: string;
    lastHandledDataset?: any;
}

interface DistributionRecordLogsDataTableState {
    isLoadingLogs: boolean;
    hasLoadedLogs: boolean;
    logs: Log[];
    pagination: Pagination;
    selectedLog: Log | null;
}

class DistributionRecordLogsDataTable extends Component<DistributionRecordLogsDataTableProps, DistributionRecordLogsDataTableState> {
    private tableRef: RefObject<HTMLDivElement>;

    constructor(props: DistributionRecordLogsDataTableProps) {
        super(props);
        this.state = {
            isLoadingLogs: true,
            hasLoadedLogs: false,
            logs: [],
            pagination: DataGridHelper.getDefaultState(25),
            selectedLog: null,
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getLogs();
    }

    componentDidUpdate(prevProps: DistributionRecordLogsDataTableProps) {
        const { lastHandledDataset } = this.props;

        if (lastHandledDataset !== prevProps.lastHandledDataset) {
            this.getLogs();
        }
    }

    getLogs = () => {
        const { pagination, hasLoadedLogs } = this.state;
        const { dataset, distribution, log, notifications } = this.props;

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
            .get(`/api/dataset/${dataset}/distribution/${distribution.slug}/log/${log}/records`, { params: filters })
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
                notifications.show(message, { variant: 'error' });
            });
    };

    handlePagination = (currentPage: number, pageSize: number) => {
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
                this.getLogs();
            },
        );
    };

    handleClick = (params: GridRowParams) => {
        const { logs } = this.state;
        const log = logs[params.id];

        this.setState({ selectedLog: log });
    };

    render() {
        const { logs, isLoadingLogs, hasLoadedLogs, pagination, selectedLog } = this.state;

        if (!hasLoadedLogs) {
            return <LoadingOverlay accessibleLabel="Loading logs" />;
        }

        const selectedLogItem = selectedLog !== null ? selectedLog : null;
        const selectedLogItemHasErrors =
            selectedLogItem !== null && selectedLogItem.errors !== null && Array.isArray(selectedLogItem.errors) && selectedLogItem.errors.length > 0;

        const columns: GridColDef[] = [
            {
                headerName: 'Record',
                field: 'record',
            },
            {
                headerName: 'Status',
                field: 'status',
            },
            {
                headerName: 'Date and time',
                field: 'createdAt',
            },
        ];

        const rows = logs.map((log, index) => ({
            id: log.id,
            record: log.record.id,
            status: <DistributionGenerationStatus status={log.status} />,
            createdAt: moment(log.createdAt).format('DD-MM-YYYY HH:mm:ss'),
        }));

        return (
            <div className="RecordLogs">
                <Split sizes={[70, 30]}>
                    <DataGridContainer
                        pagination={pagination}
                        handlePageChange={this.handlePagination}
                        fullHeight
                        isLoading={isLoadingLogs}
                        forwardRef={this.tableRef}
                        inFlexContainer
                    >
                        <DataGrid
                            disableRowSelectionOnClick
                            accessibleName="Log items"
                            emptyStateContent="No logs found"
                            onRowClick={this.handleClick}
                            rows={rows}
                            columns={columns}
                        />
                    </DataGridContainer>

                    <div className="RecordLogsDetails">
                        {selectedLogItem !== null ? (
                            <div>
                                <Typography variant="h4">
                                    Record details
                                </Typography>

                                <FormItem label="Record ID">{selectedLogItem.record.id}</FormItem>

                                <FormItem label="Status">
                                    <DistributionGenerationStatus status={selectedLogItem.status} />
                                </FormItem>

                                <FormItem
                                    label="Date and time">{moment(selectedLogItem.createdAt).format('DD-MM-YYYY HH:mm:ss')}</FormItem>

                                <FormItem label="Errors" className="ErrorLog">
                                    {selectedLogItemHasErrors && selectedLogItem.errors ? (
                                        selectedLogItem.errors.map((error, index) => (
                                            <div className="ErrorLogItem" key={`error-${index}`}>
                                                <strong>{error.exception}</strong>
                                                <br />
                                                <div>{error.message}</div>
                                            </div>
                                        ))
                                    ) : (
                                        <NoResults>No errors occurred while generating this record</NoResults>
                                    )}
                                </FormItem>
                            </div>
                        ) : (
                            <NoResults>Select a log entry</NoResults>
                        )}
                    </div>
                </Split>
            </div>
        );
    }
}

export default withNotifications(DistributionRecordLogsDataTable);