import React, {Component} from "react";
import axios from "axios/index";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {CellText, DataGrid, Heading} from "@castoredc/matter";
import moment from "moment/moment";
import DistributionGenerationStatus from "../Status/DistributionGenerationStatus";
import FormItem from "../Form/FormItem";
import DataGridHelper from "./DataGridHelper";
import DataGridContainer from "./DataGridContainer";

export default class DistributionRecordLogsDataTable extends Component {
    constructor(props) {
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

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {lastHandledDataset} = this.props;

        if (lastHandledDataset !== prevProps.lastHandledDataset) {
            this.getLogs();
        }
    }

    getLogs = () => {
        const {pagination, hasLoadedLogs} = this.state;
        const {dataset, distribution, log} = this.props;

        this.setState({
            isLoadingLogs: true,
        });

        const filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        if (hasLoadedLogs) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/log/' + log + '/records', {params: filters})
            .then((response) => {
                this.setState({
                    logs: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingLogs: false,
                    hasLoadedLogs: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingLogs: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the logs';
                toast.error(<ToastContent type="error" message={message}/>);
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
            this.getLogs();
        });
    };

    handleClick = (rowId) => {
        const {logs} = this.state;

        const log = logs[rowId];

        this.setState({selectedLog: log});
    };

    render() {
        const {logs, isLoadingLogs, hasLoadedLogs, pagination, selectedLog} = this.state;

        if (!hasLoadedLogs) {
            return <InlineLoader/>;
        }

        const selectedLogItem = selectedLog !== null ? selectedLog : null;
        const selectedLogItemHasErrors = selectedLogItem !== null && (selectedLogItem.errors !== null && (Array.isArray(selectedLogItem.errors) && selectedLogItem.errors.length > 0));

        const columns = [
            {
                Header: 'Record',
                accessor: 'record',
            },
            {
                Header: 'Status',
                accessor: 'status',
            },
            {
                Header: 'Date and time',
                accessor: 'createdAt',
            },
        ]

        const rows = logs.map((log) => {
            return {
                record: <CellText>{log.record.id}</CellText>,
                status: <CellText><DistributionGenerationStatus status={log.status}/></CellText>,
                createdAt: <CellText>{moment(log.createdAt).format('DD-MM-YYYY HH:mm:ss')}</CellText>,
            };
        });

        return <div className="RecordLogs">
            <DataGridContainer
                pagination={pagination}
                handlePageChange={this.handlePagination}
                fullHeight
                isLoading={isLoadingLogs}
                ref={this.tableRef}
                inFlexContainer
            >
                <DataGrid
                    accessibleName="Log items"
                    emptyStateContent="No logs found"
                    onClick={this.handleClick}
                    rows={rows}
                    columns={columns}
                />
            </DataGridContainer>

            <div className="RecordLogsDetails">
                {selectedLogItem !== null ? <div>
                    <Heading type="Subsection">Record details</Heading>
                    <FormItem label="Record ID">
                        {selectedLogItem.record.id}
                    </FormItem>

                    <FormItem label="Status">
                        <DistributionGenerationStatus status={selectedLogItem.status}/>
                    </FormItem>

                    <FormItem label="Date and time">
                        {moment(selectedLogItem.createdAt).format('DD-MM-YYYY HH:mm:ss')}
                    </FormItem>

                    <FormItem label="Errors" className="ErrorLog">
                        {selectedLogItemHasErrors ? selectedLogItem.errors.map((error) => {
                            return <div className="ErrorLogItem">
                                <strong>{error.exception}</strong><br/>
                                <div>
                                    {error.message}
                                </div>
                            </div>
                        }) : <div className="NoResults">No errors occurred while generating this record</div>}
                    </FormItem>
                </div> : <div className="NoResults">Select a log entry</div>}

            </div>
        </div>;
    }
}