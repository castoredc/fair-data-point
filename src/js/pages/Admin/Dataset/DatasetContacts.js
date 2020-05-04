import React, {Component} from "react";
import ContactsForm from "../../../components/Form/ContactsForm";
import Container from "react-bootstrap/Container";

export default class DatasetContacts extends Component {
    render() {
        const { catalog, dataset } = this.props;

        return <Container>
            <ContactsForm
                catalog={catalog}
                studyId={dataset.studyId}
                admin={true}
            />
        </Container>;
    }
}