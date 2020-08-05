import React, {Component} from "react";
import axios from "axios";
import {LinkContainer} from "react-router-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import Nav from "react-bootstrap/Nav";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import StudyDetails from "./StudyDetails";
import StudyContacts from "./StudyContacts";
import StudyOrganizations from "./StudyOrganizations";
import StudyConsent from "./StudyConsent";
import StudyAnnotations from "./StudyAnnotations";
import {ViewHeader} from "@castoredc/matter";
import StudyDatasets from "./StudyDatasets";
import StudyMetadata from "./StudyMetadata";
import {localizedText} from "../../../util";
import DocumentTitle from "../../../components/DocumentTitle";

export default class Study extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStudy: true,
            hasLoadedStudy: false,
            study:          null,
        };
    }

    componentDidMount() {
        this.getStudy();
    }

    getStudy = () => {
        axios.get('/api/study/' + this.props.match.params.study)
            .then((response) => {
                this.setState({
                    study:          response.data,
                    isLoadingStudy: false,
                    hasLoadedStudy: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudy: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the study';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {study, isLoadingStudy, catalog} = this.state;

        if (isLoadingStudy) {
            return <InlineLoader/>;
        }

        const title = study.hasMetadata ? localizedText(study.metadata.briefName, 'en') : null;

        return <div className="PageContainer">
            <DocumentTitle title={'FDP Admin | Study' + (title ? ` | ${title}` : '')}/>

            <div className="LeftNav">
                <Nav className="flex-column">
                    <LinkContainer to={'/admin/study/' + study.id} exact={true}>
                        <Nav.Link>Study</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/study/' + study.id + '/metadata'} exact={true}>
                        <Nav.Link>Metadata</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/study/' + study.id + '/contacts'} exact={true}>
                        <Nav.Link disabled={!study.hasMetadata}>Contacts</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/study/' + study.id + '/organizations'} exact={true}>
                        <Nav.Link disabled={!study.hasMetadata}>Centers</Nav.Link>
                    </LinkContainer>
                    <LinkContainer to={'/admin/study/' + study.id + '/consent'} exact={true}>
                        <Nav.Link disabled={!study.hasMetadata}>Consent</Nav.Link>
                    </LinkContainer>
                    <hr/>
                    <LinkContainer to={'/admin/study/' + study.id + '/datasets'} exact={true}>
                        <Nav.Link disabled={!study.hasMetadata}>Datasets</Nav.Link>
                    </LinkContainer>
                    <hr/>
                    <LinkContainer to={'/admin/study/' + study.id + '/annotations'} exact={true}>
                        <Nav.Link disabled={!study.hasMetadata}>Annotations</Nav.Link>
                    </LinkContainer>
                </Nav>
            </div>
            <div className="Page">
                <div className="PageTitle">
                    {title && <ViewHeader>{title}</ViewHeader>}
                </div>

                <Switch>
                    <Route path="/admin/study/:study" exact
                           render={(props) => <StudyDetails {...props} study={study} onSave={this.getStudy}/>}/>
                    <Route path="/admin/study/:study/metadata" exact
                           render={(props) => <StudyMetadata {...props} study={study} onSave={this.getStudy}/>}/>
                    <Route path="/admin/study/:study/contacts" exact
                           render={(props) => <StudyContacts {...props} study={study}/>}/>
                    <Route path="/admin/study/:study/organizations" exact
                           render={(props) => <StudyOrganizations {...props} study={study}/>}/>
                    <Route path="/admin/study/:study/consent" exact
                           render={(props) => <StudyConsent {...props} study={study}/>}/>
                    <Route path="/admin/study/:study/annotations" exact
                           render={(props) => <StudyAnnotations {...props} study={study}/>}/>
                    <Route path="/admin/study/:study/datasets" exact
                           render={(props) => <StudyDatasets {...props} study={study}/>}/>
                    {/*<Route path="/admin/catalog/:catalog/study/:study/distributions" exact*/}
                    {/*       render={(props) => <StudyDistributions {...props} catalog={catalog} study={study} />} />*/}
                    {/*<Route path="/admin/catalog/:catalog/study/:study/distributions/add" exact*/}
                    {/*       render={(props) => <AddDistribution {...props} catalog={catalog} study={study} />} />*/}
                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}