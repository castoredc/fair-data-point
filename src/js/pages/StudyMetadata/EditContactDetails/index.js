import React, { Component } from "react";
import axios from "axios/index";

import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import FullScreenSteppedForm from "../../../components/Form/FullScreenSteppedForm";
import OrganizationsForm from "../../../components/Form/OrganizationsForm";
import ContactsForm from "../../../components/Form/ContactsForm";

export default class EditContactDetails extends Component {
    render() {
        const numberOfSteps = 4;

        const brandText = "COVID-19 Study Database";

        return <FullScreenSteppedForm
            brandText={brandText}
            currentStep={4}
            numberOfSteps={numberOfSteps}
            smallHeading="Step Four"
            heading="Study Contact Details"
            description="Using the fields below, please tell us about the people administering your study."
        >
            <ContactsForm studyId={this.props.match.params.studyId} />
        </FullScreenSteppedForm>
    }
}