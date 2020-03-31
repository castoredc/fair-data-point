import React, {Component} from "react";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import AdminPage from "../../../components/AdminPage";

export default class StudyDetails extends Component {
    render() {
        return <AdminPage
            title="Study Details"
        >
            <StudyDetailsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} admin={true} action={this.props.match.params.action} />
        </AdminPage>
    }
}
