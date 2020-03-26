import React, {Component} from "react";
import ContactsForm from "../../../components/Form/ContactsForm";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";
import DocumentTitle from "../../../components/DocumentTitle";

export default class EditContactDetails extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.match.params.catalog}
            currentStep={4}
            smallHeading="Step Four"
            heading="Study Contact Details"
            description="Using the fields below, please tell us about the people administering your study."
        >
            <ContactsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} />
        </CatalogSteppedForm>
    }
}