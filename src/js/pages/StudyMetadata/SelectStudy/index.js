import React, { Component } from "react";
import axios from "axios/index";

import LoadingScreen from "../../../components/LoadingScreen";
import DocumentTitle from "../../../components/DocumentTitle";
import {localizedText} from "../../../util";
import {Container, Row} from "react-bootstrap";
import Contact from "../../../components/MetadataItem/Contact";
import ListItem from "../../../components/ListItem";
import Button from "react-bootstrap/Button";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";
import {ValidatorForm} from "react-form-validator-core";
import FullScreenSteppedForm from "../../../components/Form/FullScreenSteppedForm";
import LoadingSpinner from "../../../components/LoadingScreen/LoadingSpinner";
import Redirect from "react-router-dom/es/Redirect";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class AddStudy extends Component {

    constructor(props) {
        super(props);

        this.state = {
            selectedStudy: null,
            studies: {},
            isLoading: true,
            isSaved: false
        };
    }

    getStudies = () => {
        axios.get('/api/castor/studies?hide')
            .then((response) => {
                this.setState({
                    studies: response.data,
                    isLoading: false
                });
            })
            .catch((error) => {

                this.setState({
                    isLoading: false,
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    componentDidMount() {
        this.getStudies();
    }

    handleStudySelect = (studyId) => {
        this.setState({
            selectedStudy: studyId
        })
    };

    handleNext = () => {
        axios.post('/api/studies/add', {
            studyId: this.state.selectedStudy
        })
        .then((response) => {
            this.setState({
                isSaved: true
            });
        })
            .catch((error) => {
                if(error.response && error.response.status === 409)
                {
                    this.setState({
                        isSaved: true
                    });
                }
                else if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                }
            })
    };

    render() {
        const numberOfSteps = 4;

        const brandText = "COVID-19 Study Database";

        if(this.state.isLoading)
        {
            return <LoadingSpinner className="FormLoader" />;
        }

        if(this.state.isSaved)
        {
            return <Redirect to={'/my-studies/study/' + this.state.selectedStudy + '/metadata/details'} />;
        }

        return <FullScreenSteppedForm
            brandText={brandText}
            currentStep={1}
            numberOfSteps={numberOfSteps}
            smallHeading="Step One"
            heading="Choose a Study"
            description="Please choose an item from your list of studies that you’d like to include in our COVID-19 database. We only show Castor studies marked as ‘Real’ in their study settings."
        >
            {this.state.studies.length > 0 ? this.state.studies.map((study) => {
                    return <ListItem key={study.id}
                                     title={study.name}
                                     selectable={true}
                                     active={this.state.selectedStudy === study.id}
                                     onClick={() => {this.handleStudySelect(study.id)}}
                                     leftIcon="study"
                    />
                }
            ) : <div className="NoResults">No studies found.</div>}

            <div className="FullScreenSteppedFormButtons">
                <Button disabled={this.state.selectedStudy === null} onClick={this.handleNext}>Next</Button>
            </div>
        </FullScreenSteppedForm>;
    }
}
