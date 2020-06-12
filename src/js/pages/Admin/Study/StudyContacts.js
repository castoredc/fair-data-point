import React, {Component} from "react";
import ContactsForm from "../../../components/Form/ContactsForm";
import Container from "react-bootstrap/Container";

export default class StudyContacts extends Component {
    render() {
        const { study } = this.props;

        return <Container>
            <ContactsForm
                studyId={study.id}
                admin={true}
            />
        </Container>;
    }
}