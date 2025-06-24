import React, { Component } from 'react';
import { Button, Stack, Table, TableBody, TableCell, TableHead, TableRow, Box, IconButton } from '@mui/material';
import AddIcon from '@mui/icons-material/Add';
import DeleteIcon from '@mui/icons-material/Delete';
import Annotations from '../Annotations';
import NoResults from 'components/NoResults';
import AddAnnotationModal from '../../modals/AddAnnotationModal';
import './StudyStructure.scss';
import ConfirmModal from '../../modals/ConfirmModal';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface OptionGroupProps extends ComponentWithNotifications {
    studyId: string;
    id: string;
    options: any;
    onUpdate: () => void;
}

interface OptionGroupState {
    showModal: {
        add: boolean;
        remove: boolean;
    };
    modalData: {
        add: {
            type: string;
            id: string;
            title: string;
            parent: string;
        } | null;
        remove: {
            annotation: any;
            option: any;
        } | null;
    };
}

class OptionGroup extends Component<OptionGroupProps, OptionGroupState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            modalData: {
                add: null,
                remove: null,
            },
        };
    }

    openModal = (type: 'add' | 'remove', data: any) => {
        const { modalData, showModal } = this.state;

        this.setState({
            modalData: {
                ...modalData,
                [type]: data,
            },
            showModal: {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModal = (type: 'add' | 'remove') => {
        const { modalData, showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
            modalData: {
                ...modalData,
                [type]: null,
            },
        });
    };

    removeAnnotation = () => {
        const { studyId, onUpdate, notifications } = this.props;
        const { modalData } = this.state;

        if (!modalData.remove) {
            return;
        }

        apiClient
            .delete(`/api/study/${studyId}/annotations/${modalData.remove.annotation.id}`)
            .then(() => {
                notifications.show('The annotation was successfully removed', {
                    variant: 'success',
                });

                this.closeModal('remove');
                onUpdate();
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    render() {
        const { studyId, id, options, onUpdate } = this.props;
        const { showModal, modalData } = this.state;

        return (
            <>
                {modalData.add && (
                    <AddAnnotationModal
                        open={showModal.add}
                        entity={modalData.add}
                        onClose={() => this.closeModal('add')}
                        studyId={studyId}
                        onSaved={onUpdate}
                    />
                )}

                {modalData.remove && (
                    <ConfirmModal
                        show={showModal.remove}
                        title={`Delete annotation for ${modalData.remove.option.name}`}
                        action="Delete annotation"
                        variant="contained"
                        color="error"
                        onConfirm={this.removeAnnotation}
                        includeButton={false}
                    >
                        Are you sure you want to delete the
                        annotation <strong>{modalData.remove.annotation.concept.displayName}</strong>{' '}
                        <small>({modalData.remove.annotation.concept.code})</small>?
                    </ConfirmModal>
                )}

                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>Option</TableCell>
                            <TableCell>Value</TableCell>
                            <TableCell colSpan={3}>
                                <Box display="flex" gap={2}>
                                    <Box flex={1}>Ontology</Box>
                                    <Box flex={2}>Display name</Box>
                                    <Box flex={1}>Concept ID</Box>
                                    <Box sx={{ width: '40px' }}></Box>
                                </Box>
                            </TableCell>
                            <TableCell>Actions</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {options.length === 0 ? (
                            <TableRow>
                                <TableCell colSpan={4}>
                                    <NoResults>This option group does not contain options.</NoResults>
                                </TableCell>
                            </TableRow>
                        ) : (
                            options.map(option => {
                                const data = {
                                    type: 'field_option',
                                    id: option.id,
                                    title: option.name,
                                    parent: id,
                                };

                                return (
                                    <TableRow key={option.id}>
                                        <TableCell>{option.name}</TableCell>
                                        <TableCell>{option.value}</TableCell>
                                        <TableCell colSpan={3}>
                                            <Annotations
                                                annotations={option.annotations}
                                                handleRemove={annotation =>
                                                    this.openModal('remove', {
                                                        annotation,
                                                        option,
                                                    })
                                                }
                                            />
                                        </TableCell>
                                        <TableCell>
                                            <Button
                                                onClick={() => {
                                                    this.openModal('add', data);
                                                }}
                                                startIcon={<AddIcon />}
                                                variant="outlined"
                                            />
                                        </TableCell>
                                    </TableRow>
                                );
                            })
                        )}
                    </TableBody>
                </Table>
            </>
        );
    }
}

export default withNotifications(OptionGroup);