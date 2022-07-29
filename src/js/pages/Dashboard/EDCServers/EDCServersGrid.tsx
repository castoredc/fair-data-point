import React, {Component} from 'react';
import {ActionsCell, Button, CellText, DataGrid, Stack} from "@castoredc/matter";
import {ServerType} from "types/ServerType";
import ConfirmModal from "modals/ConfirmModal";
import UpdateEDCServerModal from "modals/UpdateEDCServerModal";
import AddEDCServerModal from "modals/AddEDCServerModal";
import {apiClient} from "../../../network";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";

type EDCServersGridProps = {
    edcServers: ServerType[],
}

type EDCServersGridState = {
    showAddModal: boolean,
    showUpdateModal: boolean,
    showRemoveModal: boolean,
    edcServers: ServerType[],
    selectedServer: ServerType | null;
}

export default class EDCServersGrid extends Component<EDCServersGridProps, EDCServersGridState> {
    constructor(props) {
        super(props);

        this.state = {
            showAddModal: false,
            showUpdateModal: false,
            showRemoveModal: false,
            edcServers: props.edcServers,
            selectedServer: null,
        };
    }

    openAddModal = () => {
        this.setState({
            showAddModal: true,
        });
    };

    closeAddModal = () => {
        this.setState({
            showAddModal: false,
        });
    };

    openUpdateModal = (id) => {
        this.setState({
            showUpdateModal: true,
        });
    };

    closeAllModals = () => {
        this.setState({
            showAddModal: false,
            showUpdateModal: false,
            showRemoveModal: false,
        });
    };

    handleNewServer = (newServer) => {
        const {edcServers} = this.state;

        edcServers.push(newServer);

        this.setState({
            edcServers: edcServers,
        });

        this.closeAddModal();
    }

    showUpdateModal = (existingServer) => {
        const {edcServers} = this.props;
        this.setState({
            showUpdateModal: true,
            selectedServer: existingServer
        });
    };

    handleUpdate = (updatedServer) => {
        const {edcServers, selectedServer} = this.state;

        if (! selectedServer) {
            return;
        }

        const index = edcServers.indexOf(selectedServer);
        if (index > -1) {
            let newServers = edcServers;

            newServers[index] = updatedServer;

            this.setState({
                edcServers: newServers,
            })
        }

        this.closeAllModals();
    };

    handleDeleteConfirm = (edcServer) => {
        this.setState({
            showRemoveModal: true,
            selectedServer: edcServer
        });
    }

    handleDelete = () => {
        const {edcServers, selectedServer} = this.state;
        if (! selectedServer) {
            return;
        }

        apiClient
            .delete("/api/castor/servers/" + selectedServer.id)
            .then((response) => {
                const message = `The EDC Server ${selectedServer.name} with id ${selectedServer.id} was successfully deleted`
                toast.success(
                    <ToastContent
                        type="success"
                        message={message}
                    />,
                    {
                        position: "top-right",
                    }
                );

                const index = edcServers.indexOf(selectedServer);
                if (index > -1) {
                    let newServers = edcServers;
                    newServers.splice(index, 1);

                    this.setState({
                        edcServers: newServers,
                    })
                }

            })
            .catch((error) => {
                toast.error(
                    <ToastContent type="error" message="An error occurred"/>,
                    {
                        position: "top-center",
                    }
                );
            });

        this.closeAllModals();
    };

    render() {
        const { edcServers, selectedServer, showAddModal, showUpdateModal, showRemoveModal } = this.state;

        const serverRows = edcServers.map((edcServer, index) => {
            return {
                id: <CellText>{edcServer.id}</CellText>,
                name: <CellText>{edcServer.name}</CellText>,
                url: <CellText>{(edcServer.url)}</CellText>,
                flag: <CellText>{edcServer.flag}</CellText>,
                defaultServer: <CellText>{edcServer.default ? 'Yes' : 'No'}</CellText>,
                menu: <ActionsCell
                    items={[
                        {destination: () => this.showUpdateModal(edcServer), label: 'Edit server'},
                        {destination: () => this.handleDeleteConfirm(edcServer), label: 'Remove server'}
                    ]}/>,
            }
        });

        return <div>
            <AddEDCServerModal
                open={showAddModal}
                onClose={this.closeAllModals}
                handleSave={this.handleNewServer}
            />
            <UpdateEDCServerModal
                open={showUpdateModal}
                onClose={this.closeAllModals}
                handleSave={this.handleUpdate}
                data={selectedServer}
            />

            <ConfirmModal
                title="Remove server"
                action="Remove server"
                variant="primary"
                onConfirm={this.handleDelete}
                onCancel={this.closeAllModals}
                show={showRemoveModal}
            >
                Are you sure you want remove <strong>{selectedServer && selectedServer.name}</strong> from the server list?
            </ConfirmModal>

            <Stack distribution="trailing">
                <Button icon="add" onClick={() => {
                    this.openAddModal()
                }}>
                    Add new server
                </Button>
            </Stack>

            <DataGrid
                accessibleName="EDC Servers"
                emptyStateContent="No servers available"
                rows={serverRows}
                anchorRightColumns={1}
                columns={[
                    {
                        Header: 'ID',
                        accessor: 'id',
                        width: 40,
                        maxWidth: 40,
                    },
                    {
                        Header: 'Name',
                        accessor: 'name',
                    },
                    {
                        Header: 'URL',
                        accessor: 'url',
                        minWidth: 200,
                        width: 300,
                    },
                    {
                        Header: 'Location',
                        accessor: 'flag',
                        width: 100,
                    },
                    {
                        Header: 'Default server?',
                        accessor: 'defaultServer',
                        width: 150,
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
            />
        </div>;
    }
}
