import React, { Component } from 'react';
import StudiesDataTable from 'components/DataTable/StudiesDataTable';
import { Button, Modal, Stack } from '@castoredc/matter';
import ConfirmModal from '../../../../modals/ConfirmModal';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../../../util';
import { AuthorizedRouteComponentProps } from 'components/Route';

interface AddStudyProps extends AuthorizedRouteComponentProps {
    catalog: string;
}

interface AddStudyState {
    showModal: boolean;
    selectedStudy: any;
    addedStudy: any;
}

export default class AddStudy extends Component<AddStudyProps, AddStudyState> {
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
                    showModal: true
                });
            }
        );
    };

    handleAdd = () => {
        const { catalog } = this.props;
        const { selectedStudy } = this.state;

        apiClient
            .post('/api/catalog/' + catalog + '/study/add', {
                studyId: selectedStudy.id,
            })
            .then(response => {
                toast.success(<ToastItem type="success" title="The study was successfully added to the catalog" />, {
                    position: 'top-right',
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
                toast.error(<ToastItem type="error" title={message} />);
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
                        variant="primary"
                        onConfirm={this.handleAdd}
                        onCancel={() => {
                            this.closeModal();
                        }}
                        show={showModal}
                    >
                        Are you sure you want to add <strong>{selectedStudy.hasMetadata ? localizedText(selectedStudy.metadata.title, 'en') : selectedStudy.name}</strong> to this catalog?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button
                            icon="add"
                            className="AddButton"
                            onClick={() => history.push(`/dashboard/studies/add/${catalog}`)}
                        >
                            Create new study
                        </Button>
                    </Stack>
                </div>

                <StudiesDataTable
                    onClick={this.handleStudyClick}
                    hideCatalog={catalog}
                    lastHandledStudy={addedStudy}
                />
            </PageBody>
        );
    }
}
