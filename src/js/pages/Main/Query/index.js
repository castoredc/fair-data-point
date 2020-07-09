import React, {Component} from "react";
import Header from "../../../components/Layout/Header";
import axios from "axios";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import {classNames, localizedText} from "../../../util";
import Yasqe from "@triply/yasqe";
import "@triply/yasgui/build/yasgui.min.css";
import './Query.scss';
import SPARQLDataTable from "../../../components/Yasr/SPARQLDataTable";
import Alert from "../../../components/Alert";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Button} from "@castoredc/matter";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Layout from "../../../components/Layout";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";

export default class Query extends Component {
    constructor(props) {
        super(props);

        this.state = {
            hasDistribution:       !!props.match.params.distribution,
            isLoading:             true,
            distribution:          null,
            prefixes:              [],
            columns:               [],
            rows:                  [],
            queryExecuted:         false,
            isExecutingQuery:      false,
            showEditor:            true,
            executionTime:         ''
        };
    }

    componentDidMount() {
        const {match} = this.props;

        if (match.params.distribution) {
            this.getDistribution();
        } else {
            this.createYasgui();
        }
    }

    getDistribution = () => {
        const {match} = this.props;

        axios.get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution:          response.data,
                    isLoadingDistribution: false,
                    hasLoadedDistribution: true,
                }, () => {
                    this.getPrefixes()
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistribution: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getPrefixes = () => {
        const {distribution} = this.state;

        axios.get('/api/model/' + distribution.dataModel + '/prefix')
            .then((response) => {
                let prefixes = response.data.reduce(function (map, obj) {
                    map[obj.prefix] = obj.uri;
                    return map;
                }, {});

                axios.get('https://prefix.cc/popular/all.file.json')
                    .then((response) => {
                        prefixes = {
                            prefixes,
                            ...response.data,
                        };
                        this.setState({
                            prefixes: prefixes,
                        }, () => {
                            this.createYasgui()
                        });
                    }).catch((error) => {
                        toast.error(<ToastContent type="error" message="An error occurred while loading the prefixes"/>);
                    });
            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the prefixes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    createYasgui = () => {
        const {distribution} = this.state;

        this.setState({
            isLoading: false
        }, () => {
            let config = {};

            if (distribution) {
                config['requestConfig'] = {
                    endpoint: window.location.origin + distribution.relativeUrl + '/sparql',
                }
            }

            this.yasqe = new Yasqe(document.getElementById("query"), config);

            this.yasqe.on("query", this.onQuery);
            this.yasqe.on("queryResponse", this.onResponse);
        });
    };

    onQuery = (instance, req) => {
        this.setState({
            isExecutingQuery:   true,
            queryExecuted:      false,
            error:              false,
            message:            null
        });
    };

    onResponse = (instance, req, duration) => {
        if(req instanceof Error) {
            this.setState({
                columns:        [],
                rows:           [],
                isExecutingQuery: false,
                queryExecuted:  true,
                error:          true,
                message:        req.message.replace(/&quot;/g, '"'),
                executionTime:  ''
            });
        } else {
            this.setState({
                columns:        req.body.head.vars,
                rows:           req.body.results.bindings,
                isExecutingQuery: false,
                queryExecuted:  true,
                error:          false,
                message:        null,
                executionTime:  duration
            });
        }
    };

    runQuery = () => {
        this.yasqe.query();
    };

    toggleEditor = () => {
        const { showEditor } = this.state;

        this.setState({
            showEditor: !showEditor
        });
    };

    render() {
        const { hasDistribution, isLoading, distribution, prefixes, columns, rows, queryExecuted, error, message, isExecutingQuery, showEditor, executionTime } = this.state;
        const { location, user, embedded } = this.props;

        const breadcrumbs = getBreadCrumbs(location, {distribution, query: true});

        let title = (hasDistribution && !isLoading) ? localizedText(distribution.metadata.title, 'en') : 'Query';
        const executedWithoutErrors = (queryExecuted && !error);

        return <Layout
            className="Query"
            title={title}
            isLoading={isLoading}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                <div className="QueryTools">
                    <div className={classNames('QueryEditor', !showEditor && 'Hide')} id="query"/>
                    <Row className="QueryButtons">
                        <Col>
                            {executedWithoutErrors && <div className="ResultCount"><strong>{rows.length}</strong> results in <strong>{(executionTime / 1000.0).toFixed(2)}</strong> seconds</div>}
                        </Col>
                        <Col>
                            {executedWithoutErrors && <Button onClick={this.toggleEditor}
                                                              buttonType="secondary"
                                                              fullWidth
                                                              isDropdown
                                                              isOpen={showEditor}
                                                              className="ShowHideButton"
                            >
                                {showEditor ? 'Hide' : 'Show'} query editor
                            </Button>}
                        </Col>
                        <Col>
                            <Button onClick={this.runQuery} icon="arrowPlay" className="ExecuteButton">Run query</Button>
                        </Col>
                    </Row>
                </div>
                {isExecutingQuery && <InlineLoader />}

                {executedWithoutErrors && <SPARQLDataTable
                    vars={columns}
                    bindings={rows}
                    prefixes={prefixes}
                    fullUrl={distribution.fullUrl}
                />}


                {error && <Alert variant="error" icon="errorCircled">
                    <strong>An error occurred, please check your query and try again</strong>
                    {message}
                </Alert>}
            </MainBody>

        </Layout>;
    }
}
