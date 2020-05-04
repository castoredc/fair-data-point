import React, {Component} from "react";
import OrganizationsForm from "../../../components/Form/OrganizationsForm";
import Container from "react-bootstrap/Container";

export default class DatasetOrganizations extends Component {
    render() {
        const { catalog, dataset } = this.props;

        return <Container>
            <OrganizationsForm
                catalog={catalog}
                studyId={dataset.studyId}
                admin={true}
            />
        </Container>;
    }
}