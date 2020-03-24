import React, { Component } from "react";
import axios from "axios/index";

import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import FullScreenSteppedForm from "../../../components/Form/FullScreenSteppedForm";

export default class EditStudyDetails extends Component {
    render() {
        const numberOfSteps = 4;

        const brandText = "COVID-19 Study Database";

        return <FullScreenSteppedForm
            brandText={brandText}
            currentStep={2}
            numberOfSteps={numberOfSteps}
            smallHeading="Step Two"
            heading="Study Details"
            description="Using the fields below, please provide as many accurate details as possible about your study."
        >
            <StudyDetailsForm studyId={this.props.match.params.studyId} />
        </FullScreenSteppedForm>
    }
}
