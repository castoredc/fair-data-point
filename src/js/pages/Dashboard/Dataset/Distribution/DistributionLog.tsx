import React, { Component } from 'react';
import DistributionRecordLogsDataTable from 'components/DataTable/DistributionRecordLogsDataTable';
import { Heading, LoadingOverlay } from '@castoredc/matter';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import PageTabs from 'components/PageTabs';
import * as H from 'history';

interface DistributionLogProps {
    dataset: string;
    distribution: any;
    match: { params: { log: string } };
    history: H.History;
}

interface DistributionLogState {
    showModal: boolean;
    isLoadingLog: boolean;
    hasLoadedLog: boolean;
    log: any | null;
    selectedTab: string;
}

export default class DistributionLog extends Component<DistributionLogProps, DistributionLogState> {
    constructor(props: DistributionLogProps) {
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
        const { dataset, distribution, match } = this.props;

        this.setState({
            isLoadingLog: true,
        });

        apiClient
            .get(`/api/dataset/${dataset}/distribution/${distribution.slug}/log/${match.params.log}`)
            .then(response => {
                this.setState({
                    log: response.data,
                    isLoadingLog: false,
                    hasLoadedLog: true,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingLog: false,
                });

                const message =
                    error.response?.data?.error || 'An error occurred while loading the log';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    changeTab = (tabIndex: string) => {
        this.setState({
            selectedTab: tabIndex,
        });
    };

    render() {
        const { dataset, distribution, history, match } = this.props;
        const { selectedTab, log, hasLoadedLog } = this.state;

        if (!hasLoadedLog) {
            return <LoadingOverlay accessibleLabel="Loading distribution" />;
        }

        const hasErrors = log.errors !== null && Array.isArray(log.errors) && log.errors.length > 0;

        let tabs: { [key: string]: { title: string; content: JSX.Element } } = {
            records: {
                title: 'Record logs',
                content: (
                    <DistributionRecordLogsDataTable
                        dataset={dataset}
                        distribution={distribution}
                        log={match.params.log}
                    />
                ),
            },
        };

        if (hasErrors) {
            tabs.errors = {
                title: 'Errors',
                content: (
                    <div>
                        {log.errors.map((error: any, index: number) => (
                            <div className="ErrorLogItem" key={index}>
                                <Heading type="Panel">{error.exception}</Heading>
                                <div>{error.message}</div>
                            </div>
                        ))}
                    </div>
                ),
            };
        }

        return (
            <PageBody>
                <PageTabs onChange={this.changeTab} selected={selectedTab} tabs={tabs} />
            </PageBody>
        );
    }
}