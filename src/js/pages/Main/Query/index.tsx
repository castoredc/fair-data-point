import React, { Component } from 'react';
import Header from '../../../components/Layout/Header';
import LoadingOverlay from 'components/LoadingOverlay';
import { classNames, localizedText } from '../../../util';
import Yasqe from '@triply/yasqe';
import './Query.scss';
import SPARQLDataTable from '../../../components/Yasr/SPARQLDataTable';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import Split from '../../../components/Layout/Split';
import { apiClient } from 'src/js/network';
import { AuthorizedRouteComponentProps } from 'components/Route';
import Button from '@mui/material/Button';
import PlayArrowIcon from '@mui/icons-material/PlayArrow';
import Stack from '@mui/material/Stack';
import { Alert, AlertTitle } from '@mui/material';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface QueryProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    embedded: boolean;
}

interface QueryState {
    hasDistribution: boolean;
    isLoading: boolean;
    distribution: Distribution | null;
    prefixes: { [key: string]: string };
    columns: string[];
    rows: any[];
    queryExecuted: boolean;
    isExecutingQuery: boolean;
    showEditor: boolean;
    executionTime: string | number;
    error?: boolean;
    message?: string | null;
}

interface Distribution {
    dataModel: {
        dataModel: string;
        id: string;
    };
    metadata: {
        title: string;
    };
    fullUrl: string;
    relativeUrl: string;
}

class Query extends Component<QueryProps, QueryState> {
    private yasqe: any;

    constructor(props: QueryProps) {
        super(props);

        this.state = {
            hasDistribution: !!props.match.params.distribution,
            isLoading: true,
            distribution: null,
            prefixes: {},
            columns: [],
            rows: [],
            queryExecuted: false,
            isExecutingQuery: false,
            showEditor: true,
            executionTime: '',
        };
    }

    componentDidMount() {
        const { match } = this.props;

        if (match.params.distribution) {
            this.getDistribution();
        } else {
            this.createYasgui();
        }
    }

    getDistribution = () => {
        const { match, notifications } = this.props;

        apiClient
            .get(`/api/dataset/${match.params.dataset}/distribution/${match.params.distribution}`)
            .then(response => {
                this.setState(
                    {
                        distribution: response.data,
                        isLoading: false,
                        hasDistribution: true,
                    },
                    this.getPrefixes,
                );
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the distribution';
                notifications.show(message, { variant: 'error' });
            });
    };

    getPrefixes = () => {
        const { notifications } = this.props;
        const { distribution } = this.state;

        if (!distribution) return;

        apiClient
            .get(`/api/data-model/${distribution.dataModel.dataModel}/v/${distribution.dataModel.id}/prefix`)
            .then(response => {
                let prefixes = response.data.reduce((map: { [key: string]: string }, obj: {
                    prefix: string;
                    uri: string
                }) => {
                    map[obj.prefix] = obj.uri;
                    return map;
                }, {});

                apiClient
                    .get('/static/prefixes.json')
                    .then(staticPrefixes => {
                        prefixes = {
                            ...prefixes,
                            ...staticPrefixes.data,
                        };
                        this.setState({ prefixes }, this.createYasgui);
                    })
                    .catch(() => {
                        notifications.show('An error occurred while loading the prefixes from prefix.cc', { variant: 'error' });
                        this.setState({ prefixes }, this.createYasgui);
                    });
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the prefixes from the data model';
                notifications.show(message, { variant: 'error' });
                this.createYasgui();
            });
    };

    createYasgui = () => {
        const { distribution } = this.state;

        this.setState(
            {
                isLoading: false,
            },
            () => {
                let config: any = {
                    resizable: false,
                };

                if (distribution) {
                    config['requestConfig'] = {
                        endpoint: window.location.origin + distribution.relativeUrl + '/sparql',
                    };
                }

                const element = document.getElementById('query');

                if (element) {
                    this.yasqe = new Yasqe(element, config);
                    this.yasqe.on('query', this.onQuery);
                    this.yasqe.on('queryResponse', this.onResponse);
                }
            },
        );
    };

    onQuery = () => {
        this.setState({
            isExecutingQuery: true,
            queryExecuted: false,
            error: false,
            message: null,
        });
    };

    onResponse = (instance: any, req: any, duration: string | number) => {
        if (req instanceof Error) {
            this.setState({
                columns: [],
                rows: [],
                isExecutingQuery: false,
                queryExecuted: true,
                error: true,
                message: req.message.replace(/&quot;/g, '"'),
                executionTime: '',
            });
        } else {
            this.setState({
                columns: req.body.head.vars,
                rows: req.body.results.bindings,
                isExecutingQuery: false,
                queryExecuted: true,
                error: false,
                message: null,
                executionTime: duration,
            });
        }
    };

    runQuery = () => {
        this.yasqe.query();
    };

    toggleEditor = () => {
        this.setState(prevState => ({
            showEditor: !prevState.showEditor,
        }));
    };

    render() {
        const {
            hasDistribution,
            isLoading,
            distribution,
            prefixes,
            columns,
            rows,
            queryExecuted,
            error,
            message,
            isExecutingQuery,
            showEditor,
            executionTime,
        } = this.state;
        const { location, user, embedded } = this.props;

        const title = hasDistribution && !isLoading ? localizedText(distribution?.metadata.title, 'en') : 'Query';
        const executedWithoutErrors = queryExecuted && !error;

        return (
            <Layout className="Query" embedded={embedded} fullWidth>
                <Header user={user} embedded={embedded} title={title} hideTitle={true} forceSmallHeader={true} />

                <MainBody isLoading={isLoading} className="QueryComponent">
                    <Split sizes={[40, 60]}>
                        <div className="QueryTools">
                            <div className={classNames('QueryEditor', !showEditor && 'Hide')} id="query" />
                            <Stack direction="row" sx={{ justifyContent: 'center' }}>
                                <div className="ResultCount">
                                    {executedWithoutErrors && (
                                        <>
                                            <strong>{rows.length}</strong> results
                                            in <strong>{(Number(executionTime) / 1000.0).toFixed(2)}</strong>{' '}
                                            seconds
                                        </>
                                    )}
                                </div>
                                <div>
                                    {executedWithoutErrors && (
                                        <Button
                                            onClick={this.toggleEditor}
                                            variant="outlined"
                                            className="ShowHideButton"
                                        >
                                            {showEditor ? 'Hide' : 'Show'} query editor
                                        </Button>
                                    )}
                                </div>
                                <Button onClick={this.runQuery} startIcon={<PlayArrowIcon />} className="ExecuteButton"
                                        variant="contained">
                                    Run query
                                </Button>
                            </Stack>
                        </div>

                        <div className="QueryResults">
                            {isExecutingQuery && <LoadingOverlay accessibleLabel="Loading" />}
                            {executedWithoutErrors && (
                                <SPARQLDataTable vars={columns} bindings={rows} prefixes={prefixes}
                                                 fullUrl={distribution?.fullUrl} />
                            )}

                            {error && <Alert severity="error">
                                <AlertTitle>An error occurred, please check your query and try again</AlertTitle>
                                {message}
                            </Alert>}
                        </div>
                    </Split>
                </MainBody>
            </Layout>
        );
    }
}

export default withNotifications(Query);