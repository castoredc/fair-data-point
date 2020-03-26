import React, {Component} from "react";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";

export default class Finished extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.match.params.catalog}
            currentStep={4}
            smallHeading=""
            heading="Finished"
            description="xxxxxx"
        >
        </CatalogSteppedForm>
    }
}
