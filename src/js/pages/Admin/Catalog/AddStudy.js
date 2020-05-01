import React, {Component} from "react";
import CastorStudyForm from "../../../components/Form/Admin/CastorStudyForm";
import Container from "react-bootstrap/Container";

export default class AddStudy extends Component {
    render() {
        const { catalog } = this.props;

        return <Container>
            <CastorStudyForm catalog={catalog.slug} />
        </Container>;
    }
}