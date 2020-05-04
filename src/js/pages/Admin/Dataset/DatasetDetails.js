import React, {Component} from "react";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import Container from "react-bootstrap/Container";

export default class DatasetDetails extends Component {
    render() {
        const { catalog, dataset, onSave } = this.props;

        return <Container>
            <StudyDetailsForm
                catalog={catalog}
                studyId={dataset.studyId}
                admin={true}
                onSave={onSave}
            />
        </Container>;
    }
}
