import React, { Component } from 'react';
import './DataSpecificationModules.scss';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button } from '@castoredc/matter';
import ConfirmModal from 'modals/ConfirmModal';
import SideTabs from 'components/SideTabs';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';
import MetadataFormModal from 'modals/MetadataFormModal';
import DataSpecificationForm from 'components/DataSpecification/DataSpecificationForm';
import FieldModal from 'modals/FieldModal';

interface FormsProps extends AuthorizedRouteComponentProps {
    forms: any;
    nodes: any;
    getForms: () => void;
    dataSpecification: any;
    version: any;
    type: string;
    types: {
        fieldTypes: {
            plain: {
                [key: string]: {
                    value: string,
                    label: string
                }[],
            },
            annotated: {
                value: string,
                label: string
            }[]
        },
        dataTypes: {
            value: string,
            label: string
        }[],
    };
    optionGroups: any;
}

interface FormsState {
    showModal: any;
    formModalData: any;
    fieldModalData: any;
    currentForm: any;
}

export default class Forms extends Component<FormsProps, FormsState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                field: false,
                removeField: false,
                form: false,
            },
            formModalData: null,
            fieldModalData: null,
            currentForm: null,
        };
    }

    getOrderOptions = () => {
        const { forms, type } = this.props;
        const { currentForm } = this.state;

        let order = [{
            value: 1,
            label: 'At the beginning',
        }];

        if (forms.length === 0) {
            return order;
        }

        for (let i = 0; i < forms.length; i++) {
            const item = forms[i];

            if (currentForm === null || (currentForm && item.id !== currentForm.id)) {
                const moduleNumber = i + 1;
                order.push({
                    value: moduleNumber + 1,
                    label: 'After ' + moduleNumber + ' (' + item.title + ')',
                });
            }
        }

        return order;
    };

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

    openFormModal = moduleData => {
        this.setState(
            {
                currentForm: moduleData ? moduleData : null,
                formModalData: moduleData,
            },
            () => {
                this.openModal('form');
            }
        );
    };

    openFieldModal = (module, fieldData) => {
        this.setState(
            {
                currentForm: module,
                fieldModalData: fieldData,
            },
            () => {
                this.openModal('field');
            }
        );
    };

    openRemoveFieldModal = (module, fieldData) => {
        this.setState(
            {
                currentForm: module,
                fieldModalData: fieldData,
            },
            () => {
                this.openModal('removeField');
            }
        );
    };

    onModuleSaved = () => {
        const { getForms } = this.props;
        this.closeModal('form');
        getForms();
    };

    onTripleSaved = () => {
        const { getForms } = this.props;
        this.closeModal('triple');
        getForms();
    };

    removeField = () => {
        const { type, dataSpecification, version, getForms } = this.props;
        const { currentForm, fieldModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/form/' + currentForm.id + '/field/' + fieldModalData.id)
            .then(() => {
                this.closeModal('removeField');
                getForms();
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    render() {
        const { type, dataSpecification, version, forms, nodes, types, optionGroups } = this.props;
        const { showModal, currentForm, formModalData, fieldModalData } = this.state;

        if (nodes === null) {
            return null;
        }

        const orderOptions = this.getOrderOptions();

        return (
            <PageBody>
                <MetadataFormModal
                    type={type}
                    orderOptions={orderOptions}
                    show={showModal.form}
                    handleClose={() => {
                        this.closeModal('form');
                    }}
                    onSaved={this.onModuleSaved}
                    modelId={dataSpecification.id}
                    versionId={version}
                    data={formModalData}
                />

                <FieldModal
                    open={showModal.field}
                    onClose={() => {
                        this.closeModal('field');
                    }}
                    onSaved={this.onTripleSaved}
                    modelId={dataSpecification.id}
                    versionId={version}
                    nodes={nodes}
                    data={fieldModalData}
                    form={currentForm}
                    types={types}
                    optionGroups={optionGroups}
                />

                <ConfirmModal
                    title="Delete field"
                    action="Delete field"
                    variant="danger"
                    onConfirm={this.removeField}
                    onCancel={() => this.closeModal('removeField')}
                    show={showModal.removeField}
                >
                    Are you sure you want to delete this field?
                </ConfirmModal>

                {forms.length === 0 ? (
                    <div className="NoResults">
                        This {getType(type)} does not have any forms.
                        <br />
                        <br />
                        <Button icon="add" onClick={() => this.openFormModal(null)}>
                            Add form
                        </Button>
                    </div>
                ) : (
                    <SideTabs
                        hasButtons
                        title="Forms"
                        actions={<Button icon="add" iconDescription="Add form" onClick={() => this.openFormModal(null)} />}
                        tabs={forms.map(element => {
                            let icons = [] as any;

                            return {
                                number: element.order,
                                title: element.title,
                                icons: icons,
                                content: (
                                    <DataSpecificationForm
                                        key={element.id}
                                        fields={element.fields}
                                        optionGroups={optionGroups}
                                        nodes={nodes}
                                        openFormModal={() =>
                                            this.openFormModal({
                                                id: element.id,
                                                title: element.title,
                                                order: element.order,
                                            })
                                        }
                                        openFieldModal={fieldData => this.openFieldModal(element, fieldData)}
                                        openRemoveFieldModal={fieldData => this.openRemoveFieldModal(element, fieldData)}
                                    />
                                ),
                            };
                        })}
                    />
                )}
            </PageBody>
        );
    }
}
