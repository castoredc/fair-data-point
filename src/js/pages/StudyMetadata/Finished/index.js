import React, { Component } from "react";
import axios from "axios/index";

import LoadingScreen from "../../../components/LoadingScreen";
import DocumentTitle from "../../../components/DocumentTitle";
import {localizedText} from "../../../util";
import {Container, Row} from "react-bootstrap";
import Contact from "../../../components/MetadataItem/Contact";
import ListItem from "../../../components/ListItem";
import Button from "react-bootstrap/Button";
import FullScreenSteppedForm from "../../../components/Form/FullScreenSteppedForm";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";

export default class Finished extends Component {
    render() {
        const numberOfSteps = 4;

        const brandText = "COVID-19 Study Database";

        return <FullScreenSteppedForm
            brandText={brandText}
            currentStep={4}
            numberOfSteps={numberOfSteps}
            smallHeading=""
            heading="Finished"
            description="xxxxxx"
        >
        </FullScreenSteppedForm>
    }
}
