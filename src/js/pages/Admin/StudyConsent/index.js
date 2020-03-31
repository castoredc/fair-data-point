import React, {Component} from "react";
import ConsentForm from "../../../components/Form/ConsentForm";
import AdminPage from "../../../components/AdminPage";

export default class StudyConsent extends Component {
    render() {
        return <AdminPage
            title="Consent"
        >
            <ConsentForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} admin={true} action={this.props.match.params.action} />
        </AdminPage>
    }
}