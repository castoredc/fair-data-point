import React, {Component} from "react";
import ConsentForm from "../../../components/Form/ConsentForm";
import Container from "react-bootstrap/Container";

export default class StudyConsent extends Component {
    render() {
        const { study } = this.props;

        return <Container>
            <ConsentForm
                studyId={study.id}
                admin={true}
            />
        </Container>;
    }
}