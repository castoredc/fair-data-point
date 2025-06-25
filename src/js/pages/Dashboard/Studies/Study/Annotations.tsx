import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import OptionGroup from 'components/StudyStructure/OptionGroup';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';
import { Divider, FormLabel, Stack } from '@mui/material';
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

    updateSelection = event => {
        this.setState({
            selectedOptionGroup: event.target.value,
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
            return <NoResults>This study does not have option groups.</NoResults>;
        }

        return (
            <PageBody>
                <Stack
                    direction="row"
                    spacing={2}
                    sx={{
                        justifyContent: 'flex-start',
                        alignItems: 'center',
                        mb: 2,
                    }}
                >
                    <FormLabel>Option group</FormLabel>
                    <Select
                        onChange={this.updateSelection}
                        value={selectedOptionGroup}
                        sx={{ width: 400 }}
                    >
                        {optionGroups.map(optionGroup => {
                            return <MenuItem
                                key={optionGroup.id}
                                value={optionGroup.id}>
                                {optionGroup.name}
                            </MenuItem>;
                        })}
                    </Select>
                </Stack>

                <Divider />

                {optionGroup && <OptionGroup studyId={studyId} onUpdate={this.getOptionGroups} {...optionGroup} />}
            </PageBody>
        );
    }
}

export default withNotifications(Annotations);