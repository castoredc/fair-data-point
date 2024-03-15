import React, { Component } from 'react';
import Header from '../../../components/Layout/Header';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Banner, Button, LoadingOverlay, Stack } from '@castoredc/matter';
import { classNames, localizedText } from '../../../util';
import Yasqe from '@triply/yasqe';
import './Query.scss';
import SPARQLDataTable from '../../../components/Yasr/SPARQLDataTable';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import Split from '../../../components/Layout/Split';
import { apiClient } from 'src/js/network';

export default class Query extends Component {
    constructor(props) {
        super(props);

        this.state = {
            hasDistribution: !!props.match.params.distribution,
            isLoading: true,
            distribution: null,
            prefixes: [],
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
        const { match } = this.props;

        apiClient
            .get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution)
            .then(response => {
                this.setState(
                    {
                        distribution: response.data,
                        isLoadingDistribution: false,
                        hasLoadedDistribution: true,
                    },
                    () => {
                        this.getPrefixes();
                    }
                );
            })
            .catch(error => {
                this.setState({
                    isLoadingDistribution: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the distribution';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getPrefixes = () => {
        const { distribution } = this.state;

        apiClient
            .get('/api/data-model/' + distribution.dataModel.dataModel + '/v/' + distribution.dataModel.id + '/prefix')
            .then(response => {
                let prefixes = response.data.reduce(function (map, obj) {
                    map[obj.prefix] = obj.uri;
                    return map;
                }, {});

                apiClient
                    .get('/static/prefixes.json')
                    .then(response => {
                        prefixes = {
                            prefixes,
                            ...response.data,
                        };
                        this.setState(
                            {
                                prefixes: prefixes,
                            },
                            () => {
                                this.createYasgui();
                            }
                        );
                    })
                    .catch(error => {
                        toast.error(<ToastItem type="error" title="An error occurred while loading the prefixes from prefix.cc" />);

                        this.setState(
                            {
                                prefixes: prefixes,
                            },
                            () => {
                                this.createYasgui();
                            }
                        );
                    });
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the prefixes from the data model';
                toast.error(<ToastItem type="error" title={message} />);

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
                let config = {
                    resizeable: false,
                };

                if (distribution) {
                    config['requestConfig'] = {
                        endpoint: window.location.origin + distribution.relativeUrl + '/sparql',
                    };
                }

                this.yasqe = new Yasqe(document.getElementById('query'), config);

                this.yasqe.on('query', this.onQuery);
                this.yasqe.on('queryResponse', this.onResponse);
            }
        );
    };

    onQuery = (instance, req) => {
        this.setState({
            isExecutingQuery: true,
            queryExecuted: false,
            error: false,
            message: null,
        });
    };

    onResponse = (instance, req, duration) => {
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
        const { showEditor } = this.state;

        this.setState({
            showEditor: !showEditor,
        });
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

        let title = hasDistribution && !isLoading ? localizedText(distribution.metadata.title, 'en') : 'Query';
        const executedWithoutErrors = queryExecuted && !error;

        return (
            <Layout className="Query" title={title} isLoading={isLoading} embedded={embedded} fullWidth>
                <Header user={user} embedded={embedded} title={title} hideTitle={true} forceSmallHeader={true} />

                <MainBody isLoading={isLoading} className="QueryComponent">
                    <Split sizes={[40, 60]}>
                        <div className="QueryTools">
                            <div className={classNames('QueryEditor', !showEditor && 'Hide')} id="query" />
                            <Stack className="QueryButtons" alignment="center" distribution="equalSpacing">
                                <div className="ResultCount">
                                    {executedWithoutErrors && (
                                        <>
                                            <strong>{rows.length}</strong> results in <strong>{(executionTime / 1000.0).toFixed(2)}</strong> seconds
                                        </>
                                    )}
                                </div>
                                <div>
                                    {executedWithoutErrors && (
                                        <Button
                                            onClick={this.toggleEditor}
                                            buttonType="secondary"
                                            fullWidth
                                            isDropdown
                                            isOpen={showEditor}
                                            className="ShowHideButton"
                                        >
                                            {showEditor ? 'Hide' : 'Show'} query editor
                                        </Button>
                                    )}
                                </div>
                                <Button onClick={this.runQuery} icon="arrowPlay" className="ExecuteButton">
                                    Run query
                                </Button>
                            </Stack>
                        </div>

                        <div className="QueryResults">
                            {isExecutingQuery && <LoadingOverlay accessibleLabel="Loading" />}
                            {executedWithoutErrors && (
                                <SPARQLDataTable vars={columns} bindings={rows} prefixes={prefixes} fullUrl={distribution.fullUrl} />
                            )}

                            {error && <Banner type="error" title="An error occurred, please check your query and try again" description={message} />}
                        </div>
                    </Split>
                </MainBody>
            </Layout>
        );
    }
}
