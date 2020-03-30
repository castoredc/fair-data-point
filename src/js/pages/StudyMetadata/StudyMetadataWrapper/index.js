import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import SelectStudy from "../SelectStudy";
import EditStudyDetails from "../EditStudyDetails";
import EditOrganizationDetails from "../EditOrganizationDetails";
import EditContactDetails from "../EditContactDetails";
import Finished from "../Finished";
import {Route, Switch} from "react-router-dom";
import LoadingScreen from "../../../components/LoadingScreen";
import EditConsentDetails from "../EditConsentDetails";

export default class StudyMetadataWrapper extends Component {
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
            <Route path="/my-studies/:catalog/study/add" exact
                   render={(props) => <SelectStudy {...props} catalog={this.state.catalog}/>}/>
            <Route path="/my-studies/:catalog/study/:studyId/metadata/details" exact
                   render={(props) => <EditStudyDetails {...props} catalog={this.state.catalog}/>}/>
            <Route path="/my-studies/:catalog/study/:studyId/metadata/centers" exact
                   render={(props) => <EditOrganizationDetails {...props} catalog={this.state.catalog}/>}/>
            <Route path="/my-studies/:catalog/study/:studyId/metadata/contacts" exact
                   render={(props) => <EditContactDetails {...props} catalog={this.state.catalog}/>}/>
            <Route path="/my-studies/:catalog/study/:studyId/metadata/consent" exact
                   render={(props) => <EditConsentDetails {...props} catalog={this.state.catalog}/>}/>
            <Route path="/my-studies/:catalog/study/:studyId/metadata/finished" exact
                   render={(props) => <Finished {...props} catalog={this.state.catalog}/>}/>
        </Switch>;
    }
}
