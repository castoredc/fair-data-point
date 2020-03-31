import React, {Component} from "react";
import OrganizationsForm from "../../../components/Form/OrganizationsForm";
import AdminPage from "../../../components/AdminPage";

export default class StudyOrganizations extends Component {
    render() {
        return <AdminPage
            title="Organization Details"
        >
            <OrganizationsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} admin={true} action={this.props.match.params.action} />
        </AdminPage>
    }
}