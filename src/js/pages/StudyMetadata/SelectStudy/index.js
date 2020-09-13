import React, {Component} from "react";
import axios from "axios";

import ListItem from "../../../components/ListItem";
import {Redirect} from "react-router-dom";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import LoadingScreen from "../../../components/LoadingScreen";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";
import {localizedText} from "../../../util";
import {Button, Stack} from "@castoredc/matter";

export default class AddStudy extends Component {
    constructor(props) {
        super(props);

        this.state = {
            selectedStudy:  null,
            studies:        {},
            isLoading:      true,
            isSaved:        false,
            submitDisabled: true,
            study:          null,
        };
    }

    getStudies = () => {
        axios.get('/api/castor/studies')
            .then((response) => {
                this.setState({
                    studies:   response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    componentDidMount() {
        this.getStudies();
    }

    handleStudySelect = (studyId) => {
        this.setState({
            selectedStudy:  studyId,
            submitDisabled: false,
        })
    };

    handleNext = () => {
        this.setState({
            submitDisabled: true,
        });

        axios.post('/api/catalog/' + this.props.match.params.catalog + '/study/import', {
            studyId: this.state.selectedStudy,
        })
            .then((response) => {
                this.setState({
                    isSaved: true,
                    study:   response.data,
                });
            })
            .catch((error) => {
                if (error.response && error.response.status === 409) {
                    this.setState({
                        isSaved: true,
                    });
                } else if (error.response && typeof error.response.data.error !== "undefined") {
                    this.setState({
                        submitDisabled: false,
                    });
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                }
            })
    };

    render() {
        const {isLoading, isSaved, study} = this.state;

        if (isLoading) {
            return <LoadingScreen showLoading={true}/>;
        }

        if (isSaved) {
            return <Redirect push
                             to={'/my-studies/' + this.props.match.params.catalog + '/study/' + study.id + '/metadata/details'}/>;
        }

        return <CatalogSteppedForm
            catalog={this.props.catalog}
            currentStep={1}
            smallHeading="Step One"
            heading="Choose a Study"
            description={'Please choose an item from your list of studies that you’d like to include in the ' + localizedText(this.props.catalog.metadata.title, 'en') + '.'}
        >
            <div className="FormContent">
                {this.state.studies.length > 0 ? this.state.studies.map((study) => {
                        return <ListItem key={study.sourceId}
                                         title={study.name}
                                         selectable={true}
                                         active={this.state.selectedStudy === study.sourceId}
                                         onClick={() => {
                                             this.handleStudySelect(study.sourceId)
                                         }}
                                         leftIcon="study"
                        />
                    },
                ) : <div className="NoResults">No studies found.</div>}
            </div>

            <div className="FormButtons">
                <Stack distribution="trailing">
                    <Button disabled={this.state.submitDisabled} onClick={this.handleNext}>Next</Button>
                </Stack>
            </div>
        </CatalogSteppedForm>;
    }
}
