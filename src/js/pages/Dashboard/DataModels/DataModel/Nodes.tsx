import React, {Component} from "react";
import {ActionsCell, Button, CellText, DataGrid, Icon, IconCell, Stack, Tabs} from "@castoredc/matter";
import AddNodeModal from "../../../../modals/AddNodeModal";
import ConfirmModal from "../../../../modals/ConfirmModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import {AuthorizedRouteComponentProps} from "components/Route";

interface NodesProps extends AuthorizedRouteComponentProps {
    nodes: any,
    getNodes: () => void,
    dataModel: any,
    version: any,
}

interface NodesState {
    showModal: any,
    modalData: any,
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
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
            modalData: data,
        });
    };

    closeModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    onSaved = (type) => {
        const {getNodes} = this.props;

        this.closeModal(type);

        getNodes();
    };

    removeNode = () => {
        const {dataModel, version} = this.props;
        const {modalData} = this.state;

        axios.delete('/api/model/' + dataModel.id + '/v/' + version.value + '/node/' + modalData.type + `/${modalData.id}`)
            .then(() => {
                toast.success(<ToastContent type="success" message={<>
                    The <strong>{modalData.title}</strong> node was successfully removed
                </>}
                />, {
                    position: "top-right",
                });

                this.onSaved('remove');
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);

                    this.onSaved('remove');
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    render() {
        const {showModal, modalData} = this.state;
        const {dataModel, nodes, version, history, match} = this.props;

        if (nodes === null) {
            return null;
        }

        const internalNodeRows = nodes.internal.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value}</CellText>,
                repeated: item.repeated ? <IconCell icon={{type: 'tickSmall'}}/> : undefined,
                menu: <ActionsCell items={[
                    {
                        destination: () => {
                            this.openModal('add', item)
                        },
                        label: 'Edit node',
                    },
                    {
                        destination: () => {
                            this.openModal('remove', item)
                        },
                        label: 'Delete node',
                    },
                ]}/>,
            };
        });

        const externalNodeRows = nodes.external.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                short: <CellText>{item.value.prefixedValue}</CellText>,
                uri: <CellText>{item.value.value}</CellText>,
                menu: <ActionsCell items={[
                    {
                        destination: () => {
                            this.openModal('add', item)
                        },
                        label: 'Edit node',
                    },
                    {
                        destination: () => {
                            this.openModal('remove', item)
                        },
                        label: 'Delete node',
                    },
                ]}/>,
            };
        });

        const literalNodeRows = nodes.literal.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value.value}</CellText>,
                dataType: <CellText>{item.value.dataType}</CellText>,
                menu: <ActionsCell items={[
                    {
                        destination: () => {
                            this.openModal('add', item)
                        },
                        label: 'Edit node',
                    },
                    {
                        destination: () => {
                            this.openModal('remove', item)
                        },
                        label: 'Delete node',
                    },
                ]}/>,
            };
        });

        const valueNodeRows = nodes.value.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                type: <CellText>{item.value.value}</CellText>,
                dataType: <CellText>{item.value.dataType}</CellText>,
                repeated: item.repeated ? <IconCell icon={{type: 'tickSmall'}}/> : undefined,
                menu: <ActionsCell items={[
                    {
                        destination: () => {
                            this.openModal('add', item)
                        },
                        label: 'Edit node',
                    },
                    {
                        destination: () => {
                            this.openModal('remove', item)
                        },
                        label: 'Delete node',
                    },
                ]}/>,
            };
        });

        const selectedType = match.params.nodeType;

        return <div className="PageBody">
            <AddNodeModal
                open={showModal.add}
                onClose={() => this.closeModal('add')}
                onSaved={() => this.onSaved('add')}
                type={selectedType}
                modelId={dataModel.id}
                versionId={version.value}
                data={modalData}
            />

            {modalData && <ConfirmModal
                title="Delete node"
                action="Delete node"
                variant="danger"
                onConfirm={this.removeNode}
                onCancel={() => {
                    this.closeModal('remove')
                }}
                show={showModal.remove}
            >
                Are you sure you want to delete the <strong>{modalData.title}</strong> node?
            </ConfirmModal>}

            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" onClick={() => this.openModal('add', null)}>Add {selectedType} node</Button>
                </Stack>
            </div>

            <div className="PageTabs">
                <Tabs
                    selected={match.params.nodeType}
                    onChange={(selectedKey) => {
                        const newUrl = `/dashboard/data-models/${dataModel.id}/${version.label}/nodes/${selectedKey}`;
                        history.push(newUrl);
                    }}
                    tabs={{
                        internal: {
                            title: 'Internal',
                            content: <DataGrid
                                accessibleName="Internal nodes"
                                emptyStateContent="This data model does not have internal nodes"
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
                                        Header: <Icon description="Repeated" type="tickSmall"/>,
                                        accessor: 'repeated',
                                        disableResizing: true,
                                        isInteractive: true,
                                        width: 32
                                    },
                                    {
                                        accessor: 'menu',
                                        disableGroupBy: true,
                                        disableResizing: true,
                                        isInteractive: true,
                                        isSticky: true,
                                        maxWidth: 34,
                                        minWidth: 34,
                                        width: 34
                                    }
                                ]}
                            />,
                        },
                        external: {
                            title: 'External',
                            content: <DataGrid
                                accessibleName="External nodes"
                                emptyStateContent="This data model does not have external nodes"
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
                                        width: 34
                                    }
                                ]}
                            />,
                        },
                        literal: {
                            title: 'Literal',
                            content: <DataGrid
                                accessibleName="Literal nodes"
                                emptyStateContent="This data model does not have literal nodes"
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
                                        width: 34
                                    }
                                ]}
                            />,
                        },
                        value: {
                            title: 'Value',
                            content: <DataGrid
                                accessibleName="Value nodes"
                                emptyStateContent="This data model does not have value nodes"
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
                                    {
                                        Header: <Icon description="Repeated" type="tickSmall"/>,
                                        accessor: 'repeated',
                                        disableResizing: true,
                                        isInteractive: true,
                                        width: 32
                                    },
                                    {
                                        accessor: 'menu',
                                        disableGroupBy: true,
                                        disableResizing: true,
                                        isInteractive: true,
                                        isSticky: true,
                                        maxWidth: 34,
                                        minWidth: 34,
                                        width: 34
                                    }
                                ]}
                            />,
                        },
                    }}
                />
            </div>
        </div>;
    }
}