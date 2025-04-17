import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import OptionGroup from 'components/StudyStructure/OptionGroup';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { Divider, FormLabel } from '@mui/material';
import Select from '@mui/material/Select';
import MenuItem from '@mui/material/MenuItem';

interface AnnotationsProps extends ComponentWithNotifications {
    studyId: string;
}

interface AnnotationsState {
    isLoading: boolean;
    showModal: boolean;
    optionGroups: any;
    selectedOptionGroup: string;
}

class Annotations extends Component<AnnotationsProps, AnnotationsState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            isLoading: false,
            optionGroups: [],
            selectedOptionGroup: '',
        };
    }

    componentDidMount() {
        this.getOptionGroups();
    }

    getOptionGroups = () => {
        const { studyId, notifications } = this.props;
        const { selectedOptionGroup } = this.state;
        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/castor/study/' + studyId + '/optiongroups')
            .then(response => {
                this.setState({
                    optionGroups: response.data,
                    selectedOptionGroup: selectedOptionGroup !== '' ? selectedOptionGroup : response.data.length > 0 ? response.data[0].id : '',
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    updateSelection = option => {
        this.setState({
            selectedOptionGroup: option.value,
        });
    };

    render() {
        const { studyId } = this.props;
        const { isLoading, optionGroups, selectedOptionGroup } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading option groups" />;
        }

        const optionGroup = optionGroups.find(optionGroup => {
            return optionGroup.id === selectedOptionGroup;
        });

        if (optionGroups && optionGroups.length === 0) {
            return <div className="NoResults">This study does not have option groups.</div>;
        }

        return (
            <PageBody>
                <FormLabel>Option group</FormLabel>
                <Select
                    onChange={this.updateSelection}
                    value={selectedOptionGroup}
                >
                    {optionGroups.map(optionGroup => {
                        return <MenuItem value={optionGroup.id}>{optionGroup.name}</MenuItem>
                    })}
                </Select>

                <Divider />

                {optionGroup && <OptionGroup studyId={studyId} onUpdate={this.getOptionGroups} {...optionGroup} />}
            </PageBody>
        );
    }
}

export default withNotifications(Annotations);