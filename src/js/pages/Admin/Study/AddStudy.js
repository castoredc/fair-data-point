import React, {Component} from "react";
import StudyForm from "../../../components/Form/Admin/StudyForm";
import Container from "react-bootstrap/Container";

export default class AddStudy extends Component {
    render() {
        return <Container>
            <StudyForm />
        </Container>;
    }
}