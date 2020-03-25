import React, { Component } from "react";
import axios from "axios/index";

import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import FullScreenSteppedForm from "../../../components/Form/FullScreenSteppedForm";
import OrganizationsForm from "../../../components/Form/OrganizationsForm";

export default class EditOrganizationDetails extends Component {
    render() {
        const numberOfSteps = 4;

        const brandText = "COVID-19 Study Database";

        return <FullScreenSteppedForm
            brandText={brandText}
            currentStep={3}
            numberOfSteps={numberOfSteps}
            smallHeading="Step Three"
            heading="Organization Details"
            description="Using the fields below, please tell us about the organizations participating in your study."
        >
            <OrganizationsForm studyId={this.props.match.params.studyId} />
        </FullScreenSteppedForm>
    }
}