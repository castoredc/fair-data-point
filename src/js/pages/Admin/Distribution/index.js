import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Nav from "react-bootstrap/Nav";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import DistributionDetails from "./DistributionDetails";
import DistributionContents from "./DistributionContents";
import DistributionMetadata from "./DistributionMetadata";
import {ViewHeader} from "@castoredc/matter";
import BackButton from "../../../components/BackButton";
import DistributionLogs from "./DistributionLogs";
import DistributionLog from "./DistributionLog";

export default class Distribution extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistribution: true,
            hasLoadedDistribution: false,
            distribution:          null,
            dataset:               props.match.params.dataset,
        };
    }

    componentDidMount() {
        this.getDistribution();
    }

    getDistribution = () => {
        this.setState({
            isLoadingDistribution: true,
        });

        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution:          response.data,
                    isLoadingDistribution: false,
                    hasLoadedDistribution: true,
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

    render() {
        const {distribution, isLoadingDistribution, dataset} = this.state;

        if (isLoadingDistribution) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <div className="LeftNav">
                <div className="Back">
                    <BackButton to={'/admin/dataset/' + dataset}>Back to dataset</BackButton>
                </div>

                <Nav className="flex-column">
                    <LinkContainer to={'/admin/dataset/' + dataset + '/distribution/' + distribution.slug} exact={true}>
                        <Nav.Link>Distribution</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/metadata'}
                                   exact={true}>
                        <Nav.Link>Metadata</Nav.Link>
                    </LinkContainer>
                    <hr/>

                    {distribution.type === 'rdf' && <>
                        <LinkContainer
                            to={'/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents'}
                            exact={true}>
                            <Nav.Link>Mappings</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer
                            to={'/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/log'}>
                            <Nav.Link>Log</Nav.Link>
                        </LinkContainer>
                    </>}

                    {distribution.type === 'csv' &&
                    <LinkContainer to={'/admin/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents'}
                                   exact={true}>
                        <Nav.Link>Contents</Nav.Link>
                    </LinkContainer>}

                </Nav>
            </div>
            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>{distribution.hasMetadata ? localizedText(distribution.metadata.title, 'en') : 'Distribution'}</ViewHeader>
                </div>

                <Switch>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution" exact
                           render={(props) => <DistributionDetails {...props} dataset={dataset}
                                                                   distribution={distribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/metadata" exact
                           render={(props) => <DistributionMetadata {...props} dataset={dataset}
                                                                    distribution={distribution}
                                                                    onSave={this.getDistribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/contents" exact
                           render={(props) => <DistributionContents {...props} dataset={dataset}
                                                                    distribution={distribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/log/:log" exact
                           render={(props) => <DistributionLog {...props} dataset={dataset}
                                                                distribution={distribution}/>}/>
                    <Route path="/admin/dataset/:dataset/distribution/:distribution/log" exact
                           render={(props) => <DistributionLogs {...props} dataset={dataset}
                                                                distribution={distribution}/>}/>
                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}