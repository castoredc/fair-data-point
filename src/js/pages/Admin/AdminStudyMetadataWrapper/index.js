import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Route, Switch} from "react-router-dom";
import LoadingScreen from "../../../components/LoadingScreen";
import StudyDetails from "../StudyDetails";
import StudyOrganizations from "../StudyOrganizations";
import StudyContacts from "../StudyContacts";
import StudyConsent from "../StudyConsent";

export default class AdminStudyMetadataWrapper extends Component {
    constructor(props) {
        super(props);

        this.state = {
            catalog:   null,
            isLoading: true,
        };
    }

    getCatalog = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog:   response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    componentDidMount() {
        this.getCatalog();
    }

    render() {
        const {user} = this.props;

        if (this.state.isLoading) {
            return <LoadingScreen showLoading={true}/>;
        }

        return <Switch>
            <Route path="/admin/:catalog/study/:studyId/metadata/:action/details" exact
                   render={(props) => <StudyDetails {...props} catalog={this.state.catalog}/>}/>
            <Route path="/admin/:catalog/study/:studyId/metadata/:action/centers" exact
                   render={(props) => <StudyOrganizations {...props} catalog={this.state.catalog}/>}/>
            <Route path="/admin/:catalog/study/:studyId/metadata/:action/contacts" exact
                   render={(props) => <StudyContacts {...props} catalog={this.state.catalog}/>}/>
            <Route path="/admin/:catalog/study/:studyId/metadata/:action/consent" exact
                   render={(props) => <StudyConsent {...props} catalog={this.state.catalog}/>}/>
        </Switch>;
    }
}
