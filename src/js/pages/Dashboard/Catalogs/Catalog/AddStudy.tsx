import React, { Component } from 'react';
import StudiesDataTable from 'components/DataTable/StudiesDataTable';
import Button from '@mui/material/Button';

import AddIcon from '@mui/icons-material/Add';
import ConfirmModal from '../../../../modals/ConfirmModal';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface AddStudyProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    catalog: string;
}

interface AddStudyState {
    showModal: boolean;
    selectedStudy: any;
    addedStudy: any;
}

class AddStudy extends Component<AddStudyProps, AddStudyState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            selectedStudy: null,
            addedStudy: null,
        };
    }

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    handleStudyClick = study => {
        this.setState(
            {
                selectedStudy: study,
            },
            () => {
                this.setState({
                    showModal: true,
                });
            },
        );
    };

    handleAdd = () => {
        const { catalog, notifications } = this.props;
        const { selectedStudy } = this.state;

        apiClient
            .post('/api/catalog/' + catalog + '/study/add', {
                studyId: selectedStudy.id,
            })
            .then(response => {
                notifications.show('The study was successfully added to the catalog', {
                    variant: 'success',

                });

                this.closeModal();

                this.setState({
                    addedStudy: selectedStudy,
                });
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while adding the study to the catalog';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { catalog, history } = this.props;
        const { showModal, selectedStudy, addedStudy } = this.state;

        return (
            <PageBody>
                {selectedStudy && (
                    <ConfirmModal
                        title="Add study"
                        action="Add study"
                        variant="contained"
                        onConfirm={this.handleAdd}
                        onCancel={() => {
                            this.closeModal();
                        }}
                        show={showModal}
                    >
                        Are you sure you want to add{' '}
                        <strong>{selectedStudy.hasMetadata ? localizedText(selectedStudy.metadata.title, 'en') : selectedStudy.name}</strong> to
                        this
                        catalog?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                        <Button startIcon={<AddIcon />} className="AddButton"
                                onClick={() => history.push(`/dashboard/studies/add/${catalog}`)}>
                            Create new study
                        </Button>
                    </Stack>
                </div>

                <StudiesDataTable onClick={this.handleStudyClick} hideCatalog={catalog} lastHandledStudy={addedStudy} />
            </PageBody>
        );
    }
}

export default withNotifications(AddStudy);