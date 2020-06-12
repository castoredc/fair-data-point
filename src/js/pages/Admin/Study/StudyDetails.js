import React, {Component} from "react";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import Container from "react-bootstrap/Container";

export default class StudyDetails extends Component {
    render() {
        const { study, onSave } = this.props;

        return <Container>
            <StudyDetailsForm
                studyId={study.id}
                admin={true}
                onSave={onSave}
            />
        </Container>;
    }
}
