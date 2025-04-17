import React, { Component } from 'react';
import Button from '@mui/material/Button';

import { Redirect } from 'react-router-dom';
import { apiClient } from 'src/js/network';
import StudyStructure from 'components/StudyStructure';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface CSVStudyStructureProps extends ComponentWithNotifications {
    studyId: string;
    catalog: string;
    dataset: string;
    distribution: string;
    distributionContents: string[];
}

interface CSVStudyStructureState {
    isLoadingStructure: boolean;
    structure: any;
    distributionContents: string[];
    submitDisabled: boolean;
    isSaved: boolean;
    selectedType: string;
}

class CSVStudyStructure extends Component<CSVStudyStructureProps, CSVStudyStructureState> {
    constructor(props: CSVStudyStructureProps) {
        super(props);
        this.state = {
            isLoadingStructure: true,
            structure: null,
            distributionContents: props.distributionContents,
            submitDisabled: false,
            isSaved: false,
            selectedType: 'study',
        };
    }

    handleSelect = (fieldId: string, variableName: string, label: string) => {
        let { distributionContents } = this.state;

        distributionContents = distributionContents.filter(field => {
            return !(field === fieldId);
        });

        distributionContents.push(fieldId);

        this.setState({ distributionContents });
    };

    saveDistribution = () => {
        const { catalog, dataset, distribution, notifications } = this.props;
        const { distributionContents } = this.state;

        this.setState({ submitDisabled: true });

        apiClient
            .post(`/api/dataset/${dataset}/distribution/${distribution}/contents`, distributionContents)
            .then(() => {
                this.setState({
                    isSaved: true,
                    submitDisabled: false,
                });
            })
            .catch(error => {
                const message = error.response?.data?.error || 'An error occurred while saving the distribution';
                notifications.show(message, { variant: 'error' });

                this.setState({ submitDisabled: false });
            });
    };

    render() {
        const { studyId, dataset, distribution } = this.props;
        const { distributionContents, submitDisabled, isSaved } = this.state;

        if (isSaved) {
            return <Redirect push to={`/admin/dataset/${dataset}/distribution/${distribution}`} />;
        }

        return (
            <div className="PageBody">
                <StudyStructure
                    studyId={studyId}
                    onSelect={this.handleSelect}
                    selection={distributionContents}
                    types={['study', 'report', 'survey']}
                />

                <div className="FormButtons">
                    <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                        <span className="FieldCount">
                            {distributionContents.length} field{distributionContents.length !== 1 && 's'} selected
                        </span>
                        <Button onClick={this.saveDistribution} disabled={submitDisabled}>
                            Save distribution
                        </Button>
                    </Stack>
                </div>
            </div>
        );
    }
}

export default withNotifications(CSVStudyStructure);