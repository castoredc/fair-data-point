import React, {Component} from "react";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";

export default class EditStudyDetails extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.match.params.catalog}
            currentStep={2}
            smallHeading="Step Two"
            heading="Study Details"
            description="Using the fields below, please provide as many accurate details as possible about your study."
        >
            <StudyDetailsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} />
        </CatalogSteppedForm>
    }
}
