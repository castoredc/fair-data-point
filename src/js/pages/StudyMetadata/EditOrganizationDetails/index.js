import React, {Component} from "react";
import OrganizationsForm from "../../../components/Form/OrganizationsForm";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";

export default class EditOrganizationDetails extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.match.params.catalog}
            currentStep={3}
            smallHeading="Step Three"
            heading="Organization Details"
            description="Using the fields below, please tell us about the organizations participating in your study."
        >
            <OrganizationsForm catalog={this.props.match.params.catalog} studyId={this.props.match.params.studyId} />
        </CatalogSteppedForm>
    }
}