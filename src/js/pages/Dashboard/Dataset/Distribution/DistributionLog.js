import React, {Component} from "react";
import DistributionRecordLogsDataTable from "components/DataTable/DistributionRecordLogsDataTable";
import {Heading, LoadingOverlay, Tabs} from "@castoredc/matter";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";

export default class DistributionLog extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            isLoadingLog: true,
            hasLoadedLog: false,
            log: null,
            selectedTab: 'records',
        };
    }

    componentDidMount() {
        this.getLog();
    }

    getLog = () => {
        const {dataset, distribution, match} = this.props;

        this.setState({
            isLoadingLog: true,
        });

        axios.get('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/log/' + match.params.log)
            .then((response) => {
                this.setState({
                    log: response.data,
                    isLoadingLog: false,
                    hasLoadedLog: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingLog: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the log';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    changeTab = (tabIndex) => {
        this.setState({
            selectedTab: tabIndex,
        });
    };

    render() {
        const {dataset, distribution, history, match} = this.props;
        const {selectedTab, log, hasLoadedLog} = this.state;

        if (!hasLoadedLog) {
            return <LoadingOverlay accessibleLabel="Loading distribution"/>;
        }

        const hasErrors = log.errors !== null && (Array.isArray(log.errors) && log.errors.length > 0);

        let tabs = {
            records: {
                title: 'Record logs',
                content: <DistributionRecordLogsDataTable
                    history={history}
                    dataset={dataset}
                    distribution={distribution}
                    log={match.params.log}
                />,
            },
        };

        if (hasErrors) {
            tabs.errors = {
                title: 'Errors',
                content: <div>{log.errors.map((error) => {
                    return <div className="ErrorLogItem">
                        <Heading type="Panel">{error.exception}</Heading>
                        <div>
                            {error.message}
                        </div>
                    </div>
                })}</div>
            }
        }

        return <div className="PageBody">
            <div className="PageTabs">
                <Tabs
                    onChange={this.changeTab}
                    selected={selectedTab}
                    tabs={tabs}
                />
            </div>


        </div>;
    }
}