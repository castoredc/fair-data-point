import React, { Component } from 'react';
import './DataSpecificationModules.scss';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import ConfirmModal from 'modals/ConfirmModal';
import SideTabs from 'components/SideTabs';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';
import MetadataFormModal from 'modals/MetadataFormModal';
import DataSpecificationForm from 'components/DataSpecification/DataSpecificationForm';
import FieldModal from 'modals/FieldModal';
import { Types } from 'types/Types';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';

interface FormsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    forms: any;
    nodes: any;
    getForms: () => void;
    dataSpecification: any;
    version: any;
    type: string;
    types: Types;
    optionGroups: any;
}

interface FormsState {
    showModal: any;
    formModalData: any;
    fieldModalData: any;
    currentForm: any;
}

class Forms extends Component<FormsProps, FormsState> {
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

        let order = [
            {
                value: 1,
                label: 'At the beginning',
            },
        ];

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
            },
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
            },
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
            },
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
        const { type, dataSpecification, version, getForms, notifications } = this.props;
        const { currentForm, fieldModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/form/' + currentForm.id + '/field/' + fieldModalData.id)
            .then(() => {
                this.closeModal('removeField');
                getForms();
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    render() {
        const { type, dataSpecification, version, forms, nodes, types, optionGroups, match } = this.props;
        const { showModal, currentForm, formModalData, fieldModalData } = this.state;

        if (nodes === null) {
            return null;
        }

        const initialTab = match.params.formId ? forms.findIndex(form => form.id === match.params.formId) : 0;

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
                    variant="contained"
                    color="error"
                    onConfirm={this.removeField}
                    onCancel={() => this.closeModal('removeField')}
                    show={showModal.removeField}
                >
                    Are you sure you want to delete this field?
                </ConfirmModal>

                {forms.length === 0 ? (
                    <NoResults>
                        This {getType(type)} does not have forms.
                        <br />
                        <br />
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => this.openFormModal(null)}
                            variant="contained"
                        >
                            Add form
                        </Button>
                    </NoResults>
                ) : (
                    <SideTabs
                        hasButtons
                        title="Forms"
                        actions={<Button startIcon={<AddIcon />} onClick={() => this.openFormModal(null)} />}
                        initialTab={initialTab}
                        url={`/dashboard/${type}s/${dataSpecification.id}/${match.params.version}/forms`}
                        tabs={forms.map(element => {
                            return {
                                number: element.order,
                                id: element.id,
                                title: element.title,
                                badge: element.resourceType,
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
                                                resourceType: element.resourceType,
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

export default withNotifications(Forms);