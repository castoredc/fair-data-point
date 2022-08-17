import React, { Component } from 'react';
import Annotations from '../Annotations';
import { Button, Stack } from '@castoredc/matter';
import AddAnnotationModal from '../../modals/AddAnnotationModal';
import './StudyStructure.scss';
import ConfirmModal from '../../modals/ConfirmModal';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import { apiClient } from 'src/js/network';

interface OptionGroupProps {
    studyId: string;
    id: string;
    options: any;
    onUpdate: () => void;
}

interface OptionGroupState {
    showModal: any;
    modalData: any;
}

export default class OptionGroup extends Component<OptionGroupProps, OptionGroupState> {
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

    openModal = (type, data) => {
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

    closeModal = type => {
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
        const { studyId, onUpdate } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete(`/api/study/${studyId}/annotations/${modalData.remove.annotation.id}`)
            .then(() => {
                toast.success(<ToastContent type="success" message="The annotation was successfully removed" />, {
                    position: 'top-right',
                });

                this.closeModal('remove');
                onUpdate();
            })
            .catch(error => {
                toast.error(<ToastContent type="error" message="An error occurred" />, {
                    position: 'top-center',
                });
            });
    };

    render() {
        const { studyId, id, options, onUpdate } = this.props;
        const { showModal, modalData } = this.state;

        return (
            <div className="OptionGroupTable LargeTable">
                <AddAnnotationModal
                    open={showModal.add}
                    entity={modalData.add}
                    onClose={() => this.closeModal('add')}
                    studyId={studyId}
                    onSaved={onUpdate}
                />

                {modalData.remove && (
                    <ConfirmModal
                        show={showModal.remove}
                        title={`Delete annotation for ${modalData.remove.option.name}`}
                        action="Delete annotation"
                        variant="danger"
                        onConfirm={this.removeAnnotation}
                        includeButton={false}
                    >
                        Are you sure you want to delete the annotation <strong>{modalData.remove.annotation.concept.displayName}</strong>{' '}
                        <small>({modalData.remove.annotation.concept.code})</small>?
                    </ConfirmModal>
                )}

                <div className="OptionGroupTableHeader TableHeader">
                    <div className="OptionGroupTableOption">Option</div>
                    <div className="OptionGroupTableValue">Value</div>
                    <div className="OptionGroupTableAnnotations">
                        <div className="Annotation">
                            <div className="OntologyName">Ontology</div>
                            <div className="ConceptDisplayName">Display name</div>
                            <div className="ConceptCode">Concept ID</div>
                        </div>
                    </div>
                </div>
                <div className="OptionGroupTableBody TableBody">
                    {options.length === 0 ? (
                        <div className="NoResults">This option group does not contain options.</div>
                    ) : (
                        <div>
                            {options.map(option => {
                                const data = {
                                    type: 'field_option',
                                    id: option.id,
                                    title: option.name,
                                    parent: id,
                                };

                                return (
                                    <div className="OptionGroupItem" key={option.id}>
                                        <div className="OptionGroupTableOption">{option.name}</div>
                                        <div className="OptionGroupTableValue">{option.value}</div>
                                        <div className="OptionGroupTableAnnotations">
                                            <Annotations
                                                annotations={option.annotations}
                                                handleRemove={annotation =>
                                                    this.openModal('remove', {
                                                        annotation,
                                                        option,
                                                    })
                                                }
                                            />

                                            <div className="OptionGroupTableButton">
                                                <Stack distribution="trailing">
                                                    <Button
                                                        onClick={() => {
                                                            this.openModal('add', data);
                                                        }}
                                                        icon="add"
                                                        buttonType="secondary"
                                                        iconDescription="Add annotation"
                                                    />
                                                </Stack>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        );
    }
}
