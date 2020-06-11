import React, {Component} from "react";
import StudyForm from "../../../components/Form/Admin/StudyForm";
import Container from "react-bootstrap/Container";

export default class CatalogAddStudy extends Component {
    render() {
        const { catalog } = this.props;

        return <Container>
            <StudyForm catalog={catalog} />
        </Container>;
    }
}