import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import StudyStructure from "./StudyStructure";
import {Redirect} from "react-router-dom";
import {ValidatorForm} from "react-form-validator-core";
import {Button, LoadingOverlay, Stack, Tabs} from "@castoredc/matter";

export default class CSVStudyStructure extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingStructure: true,
            hasLoadedStructure: false,
            structure: null,
            distributionContents: props.distributionContents,
            submitDisabled: false,
            isSaved: false,
            selectedType: 'study',
        };
    }

    handleSelect = (event, field, selectValue) => {
        if (event.target.tagName.toUpperCase() !== 'INPUT') {
            let {distributionContents} = this.state;

            distributionContents = distributionContents.filter(({type, value}) => {
                return !((type === 'fieldId' && value === field.id) || (type === 'variableName' && value === field.variableName))
            });

            if (selectValue === true) {
                distributionContents.push({type: 'fieldId', value: field.id});
            }

            this.setState({
                distributionContents: distributionContents,
            });
        }
    };

    saveDistribution = () => {
        const {catalog, dataset, distribution} = this.props;
        const {distributionContents} = this.state;

        this.setState({
            submitDisabled: true
        });

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution + '/contents', distributionContents)
            .then(() => {
                this.setState({
                    isSaved: true,
                    submitDisabled: false
                });
            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while saving the distribution';
                toast.error(<ToastContent type="error" message={message}/>);

                this.setState({
                    submitDisabled: false
                });
            });
    };

    render() {
        const {studyId, catalog, dataset, distribution} = this.props;
        const {structure, distributionContents, submitDisabled, isSaved, selectedType} = this.state;

        if (!this.state.hasLoadedStructure) {
            return <LoadingOverlay accessibleLabel="Loading structure"/>;
        }

        if (isSaved) {
            return <Redirect push to={'/admin/dataset/' + dataset + '/distribution/' + distribution}/>;
        }

        return <div className="PageBody">
            <ValidatorForm
                className="FullHeightForm"
                ref={node => (this.form = node)}
                onSubmit={this.saveDistribution}
            >
                <div className="PageTabs">
                    <Tabs
                        onChange={this.changeTab}
                        selected={selectedType}
                        tabs={{
                            study: {
                                title: 'Study',
                                content: <StudyStructure
                                    selectable onSelect={this.handleSelect} selection={distributionContents}
                                    studyId={studyId} contents={structure.study}
                                />,
                            },
                            report: {
                                title: 'Reports',
                                content: <StudyStructure
                                    selectable onSelect={this.handleSelect} selection={distributionContents}
                                    studyId={studyId} contents={structure.report}
                                />,
                            },
                            survey: {
                                title: 'Surveys',
                                content: <StudyStructure
                                    selectable onSelect={this.handleSelect} selection={distributionContents}
                                    studyId={studyId} contents={structure.survey}/>,
                            },
                        }}
                    />
                </div>

                <div className="FormButtons">
                    <Stack distribution="trailing" alignment="end">
                    <span
                        className="FieldCount">{distributionContents.length} field{distributionContents.length !== 1 && 's'} selected</span>
                        <Button onClick={this.saveDistribution} disabled={submitDisabled}>Save distribution</Button>
                    </Stack>
                </div>
            </ValidatorForm>
        </div>
    }

}