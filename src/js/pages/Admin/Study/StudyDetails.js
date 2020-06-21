import React, {Component} from "react";
import Container from "react-bootstrap/Container";
import StudyForm from "../../../components/Form/Admin/StudyForm";

export default class StudyDetails extends Component {
    render() {
        const { study, onSave } = this.props;

        return <Container>
            <StudyForm
                study={study}
            />
        </Container>;
    }
}
