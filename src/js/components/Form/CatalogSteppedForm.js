import React, {Component} from 'react'
import './FullScreenSteppedForm.scss'
import {localizedText} from "../../util";
import FullScreenSteppedForm from "./FullScreenSteppedForm";

export default class CatalogSteppedForm extends Component {
    render() {
        const {smallHeading, catalog, heading, description, currentStep, children} = this.props;

        return <FullScreenSteppedForm
            brandText={localizedText(catalog.metadata.title, 'en')}
            smallHeading={smallHeading}
            heading={heading}
            description={description}
            numberOfSteps={5}
            currentStep={currentStep}
            >
            {children}
        </FullScreenSteppedForm>;

    }
}