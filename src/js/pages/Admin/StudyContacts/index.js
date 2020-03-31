import React, {Component} from "react";
import ContactsForm from "../../../components/Form/ContactsForm";
import AdminPage from "../../../components/AdminPage";

export default class StudyContacts extends Component {
    render() {
        return <AdminPage
            title="Study Contact Details"
        >
            <ContactsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} admin={true} action={this.props.match.params.action} />
        </AdminPage>
    }
}