import React, {Component} from 'react'
import './FullScreenSteppedForm.scss'
import {localizedText} from "../../util";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import FullScreenSteppedForm from "./FullScreenSteppedForm";
import LoadingScreen from "../LoadingScreen";

export default class CatalogSteppedForm extends Component {
    render() {
        const {smallHeading, catalog, heading, description, currentStep, children} = this.props;

        return <FullScreenSteppedForm
            brandText={localizedText(catalog.title, 'en')}
            smallHeading={smallHeading}
            heading={heading}
            description={description}
            numberOfSteps={4}
            currentStep={currentStep}
            >
            {children}
        </FullScreenSteppedForm>;

    }
}