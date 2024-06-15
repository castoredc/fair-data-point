import React, { Component } from 'react';
import {
    ActionsCell,
    Button,
    CellText,
    DataGrid,
    Icon,
    IconCell,
    LoadingOverlay,
    Stack,
    ToastMessage,
} from '@castoredc/matter';
import NodeModal from 'modals/NodeModal';
import ConfirmModal from 'modals/ConfirmModal';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import PageTabs from 'components/PageTabs';
import { getType, ucfirst } from '../../../util';
import { Types } from 'types/Types';

interface NodesProps extends AuthorizedRouteComponentProps {
    type: string;
    nodes: any;
    getNodes: () => void;
    dataSpecification: any;
    version: any;
    types: Types,
    optionGroups: any;
    prefixes: any;
}

interface NodesState {
    showModal: any;
    modalData: any;
}

export default class Nodes extends Component<NodesProps, NodesState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            modalData: null,
        };
    }

    openModal = (type, data) => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
            modalData: data,
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

    onSaved = type => {
        const { getNodes } = this.props;

        this.closeModal(type);

        getNodes();
    };

    removeNode = () => {
        const { type, dataSpecification, version } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version.value + '/node/' + modalData.type + `/${modalData.id}`)
            .then(() => {
                toast.success(
                    <ToastMessage
                        type="success"
                        title={`The ${modalData.title} node was successfully removed`}
                    />,
                    {
                        position: 'top-right',
                    },
                );

                this.onSaved('remove');
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);

                    this.onSaved('remove');
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    render() {
        const { showModal, modalData } = this.state;
        const { type, dataSpecification, nodes, version, history, match, types, optionGroups, prefixes } = this.props;

        if (nodes === null || types.dataTypes.length === 0) {
            return <LoadingOverlay accessibleLabel="Loading data model" />;
        }

        const internalNodeRows = nodes.internal.map(item => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value}</CellText>,
                repeated: item.repeated ? <IconCell icon={{ type: 'tickSmall' }} /> : undefined,
                menu: (
                    <ActionsCell
                        items={[
                            {
                                destination: () => {
                                    this.openModal('add', item);
                                },
                                label: 'Edit node',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', item);
                                },
                                label: 'Delete node',
                            },
                        ]}
                    />
                ),
            };
        });

        const externalNodeRows = nodes.external.map(item => {
            return {
                title: <CellText>{item.title}</CellText>,
                short: <CellText>{item.value.prefixedValue}</CellText>,
                uri: <CellText>{item.value.value}</CellText>,
                menu: (
                    <ActionsCell
                        items={[
                            {
                                destination: () => {
                                    this.openModal('add', item);
                                },
                                label: 'Edit node',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', item);
                                },
                                label: 'Delete node',
                            },
                        ]}
                    />
                ),
            };
        });

        const literalNodeRows = nodes.literal.map(item => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value.value}</CellText>,
                dataType: <CellText>{item.value.dataType}</CellText>,
                menu: (
                    <ActionsCell
                        items={[
                            {
                                destination: () => {
                                    this.openModal('add', item);
                                },
                                label: 'Edit node',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', item);
                                },
                                label: 'Delete node',
                            },
                        ]}
                    />
                ),
            };
        });

        const valueNodeRows = nodes.value.map(item => {
            const dataType = item.value.dataType && types.dataTypes.find(dataType => dataType.value === item.value.dataType);

            let fieldType: {
                value: string,
                label: string
            } | undefined = undefined;

            if(item.value.fieldType && item.value.value === 'plain') {
                fieldType = types.fieldTypes.plain[item.value.dataType].find(fieldType => fieldType.value === item.value.fieldType);
            } else if(item.value.fieldType && item.value.value === 'annotated') {
                fieldType = types.fieldTypes.annotated.find(fieldType => fieldType.value === item.value.fieldType);
            }

            return {
                title: <CellText>{item.title}</CellText>,
                type: <CellText>{ucfirst(item.value.value)}</CellText>,
                dataType: <CellText>{dataType ? dataType.label : ''}</CellText>,
                ...(type === 'data-model' ? {
                    repeated: item.repeated ? <IconCell icon={{ type: 'tickSmall' }} /> : undefined
                }: {}),
                ...(type === 'metadata-model' ? {
                    fieldType: <CellText>{fieldType ? fieldType.label : ''}</CellText>,
                    optionGroup: <CellText>{item.value.optionGroup ? item.value.optionGroup.title : ''}</CellText>,
                }: {}),
                menu: (
                    <ActionsCell
                        items={[
                            {
                                destination: () => {
                                    this.openModal('add', item);
                                },
                                label: 'Edit node',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', item);
                                },
                                label: 'Delete node',
                            },
                        ]}
                    />
                ),
            };
        });

        const selectedType = match.params.nodeType;

        return (
            <PageBody>
                <NodeModal
                    modelType={type}
                    open={showModal.add}
                    onClose={() => this.closeModal('add')}
                    onSaved={() => this.onSaved('add')}
                    type={selectedType}
                    modelId={dataSpecification.id}
                    versionId={version.value}
                    data={modalData}
                    types={types}
                    optionGroups={optionGroups}
                    prefixes={prefixes}
                />

                {modalData && (
                    <ConfirmModal
                        title="Delete node"
                        action="Delete node"
                        variant="danger"
                        onConfirm={this.removeNode}
                        onCancel={() => {
                            this.closeModal('remove');
                        }}
                        show={showModal.remove}
                    >
                        Are you sure you want to delete the <strong>{modalData.title}</strong> node?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button icon="add" onClick={() => this.openModal('add', null)}>
                            Add {selectedType} node
                        </Button>
                    </Stack>
                </div>

                <PageTabs
                    selected={match.params.nodeType}
                    onChange={selectedKey => {
                        const newUrl = `/dashboard/${type}s/${dataSpecification.id}/${version.label}/nodes/${selectedKey}`;
                        history.push(newUrl);
                    }}
                    tabs={{
                        internal: {
                            title: 'Internal',
                            content: (
                                <DataGrid
                                    accessibleName="Internal nodes"
                                    emptyStateContent={`This ${getType(type)} does not have internal nodes`}
                                    rows={internalNodeRows}
                                    anchorRightColumns={1}
                                    columns={[
                                        {
                                            Header: 'Title',
                                            accessor: 'title',
                                        },
                                        {
                                            Header: 'Slug',
                                            accessor: 'value',
                                        },
                                        {
                                            Header: <Icon description="Repeated" type="tickSmall" />,
                                            accessor: 'repeated',
                                            disableResizing: true,
                                            isInteractive: true,
                                            width: 32,
                                        },
                                        {
                                            accessor: 'menu',
                                            disableGroupBy: true,
                                            disableResizing: true,
                                            isInteractive: true,
                                            isSticky: true,
                                            maxWidth: 34,
                                            minWidth: 34,
                                            width: 34,
                                        },
                                    ]}
                                />
                            ),
                        },
                        external: {
                            title: 'External',
                            content: (
                                <DataGrid
                                    accessibleName="External nodes"
                                    emptyStateContent={`This ${getType(type)} does not have external nodes`}
                                    rows={externalNodeRows}
                                    anchorRightColumns={1}
                                    columns={[
                                        {
                                            Header: 'Title',
                                            accessor: 'title',
                                        },
                                        {
                                            Header: 'Short',
                                            accessor: 'short',
                                        },
                                        {
                                            Header: 'URI',
                                            accessor: 'uri',
                                        },
                                        {
                                            accessor: 'menu',
                                            disableGroupBy: true,
                                            disableResizing: true,
                                            isInteractive: true,
                                            isSticky: true,
                                            maxWidth: 34,
                                            minWidth: 34,
                                            width: 34,
                                        },
                                    ]}
                                />
                            ),
                        },
                        literal: {
                            title: 'Literal',
                            content: (
                                <DataGrid
                                    accessibleName="Literal nodes"
                                    emptyStateContent={`This ${getType(type)} does not have literal nodes`}
                                    rows={literalNodeRows}
                                    anchorRightColumns={1}
                                    columns={[
                                        {
                                            Header: 'Title',
                                            accessor: 'title',
                                        },
                                        {
                                            Header: 'Value',
                                            accessor: 'value',
                                        },
                                        {
                                            Header: 'Data type',
                                            accessor: 'dataType',
                                        },
                                        {
                                            accessor: 'menu',
                                            disableGroupBy: true,
                                            disableResizing: true,
                                            isInteractive: true,
                                            isSticky: true,
                                            maxWidth: 34,
                                            minWidth: 34,
                                            width: 34,
                                        },
                                    ]}
                                />
                            ),
                        },
                        value: {
                            title: 'Value',
                            content: (
                                <DataGrid
                                    accessibleName="Value nodes"
                                    emptyStateContent={`This ${getType(type)} does not have value nodes`}
                                    rows={valueNodeRows}
                                    anchorRightColumns={1}
                                    columns={[
                                        {
                                            Header: 'Title',
                                            accessor: 'title',
                                        },
                                        {
                                            Header: 'Type of value',
                                            accessor: 'type',
                                        },
                                        {
                                            Header: 'Data type',
                                            accessor: 'dataType',
                                        },
                                        ...(type === 'data-model' ? [{
                                            Header: <Icon description="Repeated" type="tickSmall" />,
                                            accessor: 'repeated',
                                            disableResizing: true,
                                            isInteractive: true,
                                            width: 32,
                                        }] : []),
                                        {
                                            accessor: 'menu',
                                            disableGroupBy: true,
                                            disableResizing: true,
                                            isInteractive: true,
                                            isSticky: true,
                                            maxWidth: 34,
                                            minWidth: 34,
                                            width: 34,
                                        },
                                    ]}
                                />
                            ),
                        },
                    }}
                />
            </PageBody>
        );
    }
}
