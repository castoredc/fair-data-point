import React, {Component} from "react";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";
import ConsentForm from "../../../components/Form/ConsentForm";

export default class EditConsentDetails extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.catalog}
            currentStep={5}
            smallHeading="Step Five"
            heading="Consent"
            description="Please let us know if you consent to sharing high-level information about your study and promoting your study on social media."
        >
            <ConsentForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} />
        </CatalogSteppedForm>
    }
}