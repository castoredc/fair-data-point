import React, {Component} from "react";
import OrganizationsForm from "../../../components/Form/OrganizationsForm";
import Container from "react-bootstrap/Container";

export default class StudyOrganizations extends Component {
    render() {
        const { study } = this.props;

        return <Container>
            <OrganizationsForm
                studyId={study.id}
                admin={true}
            />
        </Container>;
    }
}