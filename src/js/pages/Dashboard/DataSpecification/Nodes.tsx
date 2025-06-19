import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import AddIcon from '@mui/icons-material/Add';
import NodeModal from 'modals/NodeModal';
import ConfirmModal from 'modals/ConfirmModal';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import PageTabs from 'components/PageTabs';
import { getType, ucfirst } from '../../../util';
import { Types } from 'types/Types';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import DataGrid from 'components/DataTable/DataGrid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import CheckIcon from '@mui/icons-material/Check';

interface NodesProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
    nodes: any;
    getNodes: () => void;
    dataSpecification: any;
    version: any;
    types: Types;
    optionGroups: any;
    prefixes: any;
}

interface NodesState {
    showModal: any;
    modalData: any;
}

class Nodes extends Component<NodesProps, NodesState> {
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
        const { type, dataSpecification, version, notifications } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version.value + '/node/' + modalData.type + `/${modalData.id}`)
            .then(() => {
                notifications.show(`The ${modalData.title} node was successfully removed`, {
                    variant: 'success',

                });

                this.onSaved('remove');
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });

                    this.onSaved('remove');
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
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
                id: item.id,
                title: item.title,
                value: item.value,
                repeated: item.repeated,
                data: item,
            };
        });

        const externalNodeRows = nodes.external.map(item => {
            return {
                id: item.id,
                title: item.title,
                short: item.value.prefixedValue,
                uri: item.value.value,
                data: item,
            };
        });

        const literalNodeRows = nodes.literal.map(item => {
            return {
                id: item.id,
                title: item.title,
                value: item.value.value,
                dataType: item.value.dataType,
                data: item,
            };
        });

        const valueNodeRows = nodes.value.map(item => {
            const dataType = item.value.dataType && types.dataTypes.find(dataType => dataType.value === item.value.dataType);

            let fieldType:
                | {
                value: string;
                label: string;
            }
                | undefined = undefined;

            if (item.value.fieldType && item.value.value === 'plain') {
                fieldType = types.fieldTypes.plain[item.value.dataType].find(fieldType => fieldType.value === item.value.fieldType);
            } else if (item.value.fieldType && item.value.value === 'annotated') {
                fieldType = types.fieldTypes.annotated.find(fieldType => fieldType.value === item.value.fieldType);
            }

            return {
                id: item.id,
                title: item.title,
                type: ucfirst(item.value.value),
                dataType: dataType ? dataType.label : '',
                ...(type === 'data-model'
                    ? {
                        repeated: item.repeated,
                    }
                    : {}),
                ...(type === 'metadata-model'
                    ? {
                        fieldType: fieldType ? fieldType.label : '',
                        optionGroup: item.value.optionGroup ? item.value.optionGroup.title : '',
                    }
                    : {}),
                data: item,
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
                        variant="contained"
                        color="error"
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
                    <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => this.openModal('add', null)}
                            variant="contained"
                        >
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
                                    disableRowSelectionOnClick
                                    accessibleName="Internal nodes"
                                    emptyStateContent={`This ${getType(type)} does not have internal nodes`}
                                    rows={internalNodeRows}
                                    columns={[
                                        {
                                            headerName: 'Title',
                                            field: 'title',
                                        },
                                        {
                                            headerName: 'Slug',
                                            field: 'value',
                                        },
                                        {
                                            headerName: 'Repeated',
                                            field: 'repeated',
                                            resizable: false,
                                            // isInteractive: true,
                                            width: 32,
                                            renderCell: (params) => {
                                                return params.row.repeated ? <CheckIcon /> : '';
                                            },
                                        },
                                        {
                                            field: 'actions',
                                            headerName: '',
                                            width: 80,
                                            sortable: false,
                                            disableColumnMenu: true,
                                            align: 'right',
                                            cellClassName: 'actionsCell',
                                            renderCell: (params) => {
                                                return <RowActionsMenu
                                                    row={params.row}

                                                    items={[
                                                        {
                                                            destination: () => {
                                                                this.openModal('add', params.row.data);
                                                            },
                                                            label: 'Edit node',
                                                        },
                                                        {
                                                            destination: () => {
                                                                this.openModal('remove', params.row.data);
                                                            },
                                                            label: 'Delete node',
                                                        },
                                                    ]}
                                                />;
                                            },
                                        },
                                    ]}
                                />
                            ),
                        },
                        external: {
                            title: 'External',
                            content: (
                                <DataGrid
                                    disableRowSelectionOnClick
                                    accessibleName="External nodes"
                                    emptyStateContent={`This ${getType(type)} does not have external nodes`}
                                    rows={externalNodeRows}
                                    columns={[
                                        {
                                            headerName: 'Title',
                                            field: 'title',
                                        },
                                        {
                                            headerName: 'Short',
                                            field: 'short',
                                        },
                                        {
                                            headerName: 'URI',
                                            field: 'uri',
                                        },
                                        {
                                            field: 'actions',
                                            headerName: '',
                                            width: 80,
                                            sortable: false,
                                            disableColumnMenu: true,
                                            align: 'right',
                                            cellClassName: 'actionsCell',
                                            renderCell: (params) => {
                                                return <RowActionsMenu
                                                    row={params.row}

                                                    items={[
                                                        {
                                                            destination: () => {
                                                                this.openModal('add', params.row.data);
                                                            },
                                                            label: 'Edit node',
                                                        },
                                                        {
                                                            destination: () => {
                                                                this.openModal('remove', params.row.data);
                                                            },
                                                            label: 'Delete node',
                                                        },
                                                    ]}
                                                />;
                                            },
                                        },
                                    ]}
                                />
                            ),
                        },
                        literal: {
                            title: 'Literal',
                            content: (
                                <DataGrid
                                    disableRowSelectionOnClick
                                    accessibleName="Literal nodes"
                                    emptyStateContent={`This ${getType(type)} does not have literal nodes`}
                                    rows={literalNodeRows}
                                    // anchorRightColumns={1}
                                    columns={[
                                        {
                                            headerName: 'Title',
                                            field: 'title',
                                        },
                                        {
                                            headerName: 'Value',
                                            field: 'value',
                                        },
                                        {
                                            headerName: 'Data type',
                                            field: 'dataType',
                                        },
                                        {
                                            field: 'actions',
                                            headerName: '',
                                            width: 80,
                                            sortable: false,
                                            disableColumnMenu: true,
                                            align: 'right',
                                            cellClassName: 'actionsCell',
                                            renderCell: (params) => {
                                                return <RowActionsMenu
                                                    row={params.row}

                                                    items={[
                                                        {
                                                            destination: () => {
                                                                this.openModal('add', params.row.data);
                                                            },
                                                            label: 'Edit node',
                                                        },
                                                        {
                                                            destination: () => {
                                                                this.openModal('remove', params.row.data);
                                                            },
                                                            label: 'Delete node',
                                                        },
                                                    ]}
                                                />;
                                            },
                                        },
                                    ]}
                                />
                            ),
                        },
                        value: {
                            title: 'Value',
                            content: (
                                <DataGrid
                                    disableRowSelectionOnClick
                                    accessibleName="Value nodes"
                                    emptyStateContent={`This ${getType(type)} does not have value nodes`}
                                    rows={valueNodeRows}
                                    // anchorRightColumns={1}
                                    columns={[
                                        {
                                            headerName: 'Title',
                                            field: 'title',
                                        },
                                        {
                                            headerName: 'Type of value',
                                            field: 'type',
                                        },
                                        {
                                            headerName: 'Data type',
                                            field: 'dataType',
                                        },
                                        ...(type === 'data-model'
                                            ? [
                                                {
                                                    headerName: 'Repeated',
                                                    field: 'repeated',
                                                    resizable: false,
                                                    // isInteractive: true,
                                                    width: 32,
                                                },
                                            ]
                                            : []),
                                        {
                                            field: 'actions',
                                            headerName: '',
                                            width: 80,
                                            sortable: false,
                                            disableColumnMenu: true,
                                            align: 'right',
                                            cellClassName: 'actionsCell',
                                            renderCell: (params) => {
                                                return <RowActionsMenu
                                                    row={params.row}

                                                    items={[
                                                        {
                                                            destination: () => {
                                                                this.openModal('add', params.row.data);
                                                            },
                                                            label: 'Edit node',
                                                        },
                                                        {
                                                            destination: () => {
                                                                this.openModal('remove', params.row.data);
                                                            },
                                                            label: 'Delete node',
                                                        },
                                                    ]}
                                                />;
                                            },
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

export default withNotifications(Nodes);