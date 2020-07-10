import React, {Component} from "react";
import ContactsForm from "../../../components/Form/Study/ContactsForm";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";

export default class EditContactDetails extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.catalog}
            currentStep={4}
            smallHeading="Step Four"
            heading="Study Contact Details"
            description="Using the fields below, please tell us about the people administering your study."
        >
            <ContactsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} />
        </CatalogSteppedForm>
    }
}