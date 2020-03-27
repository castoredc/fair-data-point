import React, {Component} from "react";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";
import {localizedText} from "../../../util";
import Button from "react-bootstrap/Button";

export default class Finished extends Component {
    render() {
        return <CatalogSteppedForm
            catalog={this.props.catalog}
            heading="Thanks, all done!"
            description="Your study has been successfully submitted and is now available in our public database."
        >
            <Button variant="primary" href={'/fdp/' + this.props.catalog.slug}>Visit the {localizedText(this.props.catalog.title, 'en')}</Button>
        </CatalogSteppedForm>
    }
}
