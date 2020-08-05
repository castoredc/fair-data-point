import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {DataTable, Pagination} from "@castoredc/matter";
import {classNames} from "../../util";
import moment from "moment";
import DistributionGenerationStatus from "../Status/DistributionGenerationStatus";

export default class DistributionLogsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingLogs: true,
            hasLoadedLogs: false,
            logs:          [],
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
        const {dataset, distribution} = this.props;

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

        axios.get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/log', {params: filters})
            .then((response) => {
                this.setState({
                    logs:          response.data.results,
                    pagination:        {
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
        const {dataset, distribution, history} = this.props;

        if (typeof index !== "undefined" && logs.length > 0) {
            const log = logs.find((item) => item.id === rowID);

            history.push({
                pathname: '/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/log/' + log.id,
            });
        }
    };

    render() {
        const {logs, isLoadingLogs, hasLoadedLogs, pagination} = this.state;

        if (!hasLoadedLogs) {
            return <InlineLoader/>;
        }

        const rows = new Map(logs.map((log) => {
            return [
                log.id,
                {
                    cells: [
                        moment(log.createdAt).format('DD-MM-YYYY HH:mm:ss'),
                        <DistributionGenerationStatus status={log.status}/>,
                        log.records.total
                    ],
                }];
        }));

        return <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingLogs && 'Loading')}
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
                            header:    'Date and time',
                            resizable: true,
                            template:  'text',
                        },
                        description: {
                            header:    'Status',
                            resizable: true,
                            template:  'text',
                        },
                        language:    {
                            header:    'Records',
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

        </div>;
    }
}