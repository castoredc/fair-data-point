import React, { Component } from 'react';
import Header from '../../../components/Layout/Header';
import LoadingOverlay from 'components/LoadingOverlay';
import { localizedText } from '../../../util';
import Yasqe from '@triply/yasqe';
import SPARQLDataTable from '../../../components/Yasr/SPARQLDataTable';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import Split from '../../../components/Layout/Split';
import { apiClient } from 'src/js/network';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { Button, Stack, Alert, AlertTitle, Box } from '@mui/material';
import PlayArrowIcon from '@mui/icons-material/PlayArrow';
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
            <Layout embedded={embedded} fullWidth>
                <Header user={user} embedded={embedded} title={title} hideTitle={true} forceSmallHeader={true} />
                <MainBody isLoading={isLoading}>
                    <Box height="100vh" display="flex" flexDirection="column" overflow="hidden" width="100%">
                        <Split sizes={[40, 60]}>
                            <Stack height="100%">
                                <Box
                                    flex={1}
                                    id="query"
                                    className={showEditor ? undefined : 'Hide'}
                                    sx={{
                                        '& .yasqe': {
                                            transition: 'all 0.8s ease',
                                            height: '100%',
                                            borderRadius: 3,
                                        },
                                        '& .yasqe_queryButton': {
                                            display: 'none !important',
                                        },
                                        '& .yasgui .controlbar': {
                                            display: 'none',
                                        },
                                        '& .CodeMirror': {
                                            fontFamily: '"Fira Code", monospace',
                                            fontSize: 13,
                                            height: '100% !important',
                                            borderRadius: 4,
                                        },
                                        '&.Hide .yasqe': {
                                            maxHeight: '0 !important',
                                        },
                                    }}
                                />
                                <Box mt={1} mb={0}>
                                    <Stack direction="row" justifyContent="space-between" alignItems="center">
                                        <Box sx={{ lineHeight: '30px' }}>
                                            {executedWithoutErrors && (
                                                <>
                                                    <strong>{rows.length}</strong> results
                                                    in <strong>{(Number(executionTime) / 1000.0).toFixed(2)}</strong>{' '}
                                                    seconds
                                                </>
                                            )}
                                        </Box>
                                        <Stack direction="row" spacing={2}>
                                            {executedWithoutErrors && (
                                                <Button
                                                    onClick={this.toggleEditor}
                                                    variant="outlined"
                                                >
                                                    {showEditor ? 'Hide' : 'Show'} query editor
                                                </Button>
                                            )}
                                            <Button
                                                variant="contained"
                                                startIcon={<PlayArrowIcon />}
                                                onClick={() => this.yasqe.query()}
                                                disabled={isExecutingQuery}
                                            >
                                                {localizedText('Execute query')}
                                            </Button>
                                        </Stack>
                                    </Stack>
                                </Box>
                            </Stack>
                            <Box height="100%" sx={{
                                '& .DataTableWrapper': {
                                    height: '100%',
                                    borderRadius: 1,
                                    overflow: 'hidden',
                                    border: '1px solid',
                                    borderColor: 'divider',
                                },
                                '& a': {
                                    textDecoration: 'none',
                                },
                            }}>
                                {isExecutingQuery && <LoadingOverlay accessibleLabel="Loading" />}
                                {queryExecuted && (
                                    <Box sx={{ height: '100%' }}>
                                        {error ? (
                                            <Alert severity="error">
                                                <AlertTitle>Error</AlertTitle>
                                                {message}
                                            </Alert>
                                        ) : (
                                            <Box className="DataTableWrapper">
                                                <SPARQLDataTable
                                                    vars={columns}
                                                    bindings={rows}
                                                    prefixes={prefixes}
                                                    fullUrl={distribution?.fullUrl}
                                                />
                                            </Box>
                                        )}
                                    </Box>
                                )}
                            </Box>
                        </Split>
                    </Box>
                </MainBody>
            </Layout>
        );
    }
}

export default withNotifications(Query);