import React, {Component} from "react";
import ConsentForm from "../../../components/Form/ConsentForm";
import Container from "react-bootstrap/Container";

export default class DatasetConsent extends Component {
    render() {
        const { catalog, dataset } = this.props;

        return <Container>
            <ConsentForm
                catalog={catalog}
                studyId={dataset.studyId}
                admin={true}
            />
        </Container>;
    }
}