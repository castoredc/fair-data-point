import React, { Component } from 'react';
import StudiesDataTable from 'components/DataTable/StudiesDataTable';
import { Button, Modal, Stack } from '@castoredc/matter';
import ConfirmModal from '../../../../modals/ConfirmModal';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import StudyForm from 'components/Form/Admin/StudyForm';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import { localizedText } from '../../../../util';

interface AddStudyProps {
    catalog: string;
}

interface AddStudyState {
    showModal: any;
    selectedStudy: any;
    addedStudy: any;
}

export default class AddStudy extends Component<AddStudyProps, AddStudyState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                newStudy: false,
                confirm: false,
            },
            selectedStudy: null,
            addedStudy: null,
        };
    }

    openModal = type => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModal = type => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    handleStudyClick = study => {
        this.setState(
            {
                selectedStudy: study,
            },
            () => {
                this.openModal('confirm');
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

                this.closeModal('confirm');

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
        const { catalog } = this.props;
        const { showModal, selectedStudy, addedStudy } = this.state;

        return (
            <PageBody>
                <Modal
                    open={showModal.newStudy}
                    onClose={() => {
                        this.closeModal('newStudy');
                    }}
                    title="Add new study"
                    accessibleName="Add new study"
                >
                    <StudyForm />
                </Modal>

                {selectedStudy && (
                    <ConfirmModal
                        title="Add study"
                        action="Add study"
                        variant="primary"
                        onConfirm={this.handleAdd}
                        onCancel={() => {
                            this.closeModal('confirm');
                        }}
                        show={showModal.confirm}
                    >
                        Are you sure you want to add <strong>{selectedStudy.hasMetadata ? localizedText(selectedStudy.metadata.title, 'en') : selectedStudy.name}</strong> to this catalog?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button
                            icon="add"
                            className="AddButton"
                            onClick={() => {
                                this.openModal('newStudy');
                            }}
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
