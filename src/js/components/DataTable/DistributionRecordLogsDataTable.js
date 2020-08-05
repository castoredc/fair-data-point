import React, {Component} from "react";
import axios from "axios/index";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify/index";
import ToastContent from "../ToastContent";
import {DataTable, Heading, Pagination} from "@castoredc/matter/lib/matter.esm";
import {classNames} from "../../util";
import moment from "moment/moment";
import DistributionGenerationStatus from "../Status/DistributionGenerationStatus";
import FormItem from "../Form/FormItem";

export default class DistributionRecordLogsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingLogs: true,
            hasLoadedLogs: false,
            logs:          [],
            pagination:    {
                currentPage:  1,
                start:        1,
                perPage:      25,
                totalResults: null,
                totalPages:   null,
            },
            selectedLog:   null,
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
            page:    pagination.currentPage,
            perPage: pagination.perPage,
        };

        if (hasLoadedLogs) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/log/' + log + '/records', {params: filters})
            .then((response) => {
                this.setState({
                    logs:          response.data.results,
                    pagination:    {
                        currentPage:  response.data.currentPage,
                        perPage:      response.data.perPage,
                        start:        response.data.start,
                        totalResults: response.data.totalResults,
                        totalPages:   response.data.totalPages,
                    },
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
                currentPage: paginationCount.currentPage,
                perPage:     paginationCount.pageLimit,
            },
        }, () => {
            this.getLogs();
        });
    };

    handleClick = (event, rowID, index) => {
        const {logs} = this.state;

        if (typeof index !== "undefined" && logs.length > 0) {
            const log = logs.find((item) => item.id === rowID);

            this.setState({selectedLog: log});
        }
    };

    render() {
        const {logs, isLoadingLogs, hasLoadedLogs, pagination, selectedLog} = this.state;

        if (!hasLoadedLogs) {
            return <InlineLoader/>;
        }

        const selectedLogItem = selectedLog !== null ? selectedLog : null;
        const selectedLogItemHasErrors = selectedLogItem !== null && (selectedLogItem.errors !== null && (Array.isArray(selectedLogItem.errors) && selectedLogItem.errors.length > 0));

        const rows = new Map(logs.map((log) => {
            return [
                log.id,
                {
                    cells: [
                        log.record.id,
                        <DistributionGenerationStatus status={log.status}/>,
                        moment(log.createdAt).format('DD-MM-YYYY HH:mm:ss'),
                    ],
                }];
        }));

        return <div className="RecordLogs">
            <div
                className={classNames('SelectableDataTable FullHeightDataTable RecordLogsDataTable', isLoadingLogs && 'Loading')}
                ref={this.tableRef}>
                <div className="DataTableWrapper">
                    <DataTable
                        emptyTableMessage="No logs found"
                        highlightRowOnHover
                        cellSpacing="default"
                        onClick={this.handleClick}
                        rows={rows}
                        structure={{
                            title:       {
                                header:    'Record',
                                resizable: true,
                                template:  'text',
                            },
                            description: {
                                header:    'Status',
                                resizable: true,
                                template:  'text',
                            },
                            language:    {
                                header:    'Date and time',
                                resizable: true,
                                template:  'text',
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