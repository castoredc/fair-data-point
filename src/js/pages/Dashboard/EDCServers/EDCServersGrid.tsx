import React, { useEffect, useMemo, useState } from 'react';
import { ActionsCell, Button, CellText, DataGrid, LoadingOverlay, Stack } from '@castoredc/matter';
import { ServerType } from 'types/ServerType';
import ConfirmModal from 'modals/ConfirmModal';
import { UpdateEDCServerModal } from 'modals/UpdateEDCServerModal';
import { AddEDCServerModal } from 'modals/AddEDCServerModal';
import { apiClient } from '../../../network';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';

const EDCServersGrid = () => {
    const [showAddModal, setShowAddModal] = useState(false);
    const [showUpdateModal, setShowUpdateModal] = useState(false);
    const [showRemoveModal, setShowRemoveModal] = useState(false);
    const [edcServersState, setEdcServersState] = useState<ServerType[]>([]);
    const [selectedServer, setSelectedServer] = useState<ServerType>();
    const [isLoading, setIsLoading] = useState(false);

    const getEDCServers = () => {
        setIsLoading(true);

        apiClient
            .get('/api/castor/servers')
            .then(response => {
                setIsLoading(false);
                setEdcServersState(response.data);
            })
            .catch(error => {
                setIsLoading(false);
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred while loading the EDC Servers information" />);
                }
            });
    };

    useEffect(() => {
        getEDCServers();
    }, []);

    const openAddModal = () => {
        setShowAddModal(true);
    };

    const closeAddModal = () => {
        setShowAddModal(false);
    };

    const closeAllModals = () => {
        setShowAddModal(false);
        setShowUpdateModal(false);
        setShowRemoveModal(false);
    };

    const openUpdateModal = existingServer => {
        setShowUpdateModal(true);
        setSelectedServer(existingServer);
    };

    const handleNewServer = newServer => {
        setEdcServersState([...edcServersState, newServer]);

        closeAddModal();
    };

    const handleUpdate = updatedServer => {
        if (!selectedServer) {
            return;
        }

        // const index = edcServersState.indexOf(selectedServer);
        // if (index > -1) {
        //     let newServers = edcServersState;
        //
        //     newServers[index] = updatedServer;
        //     setEdcServersState(newServers);
        // }

        getEDCServers();

        closeAllModals();
    };

    const handleDeleteConfirm = edcServer => {
        setShowRemoveModal(true);
        setSelectedServer(edcServer);
    };

    const handleDelete = () => {
        if (!selectedServer) {
            return;
        }

        apiClient
            .delete('/api/castor/servers/' + selectedServer.id)
            .then(response => {
                const message = `The EDC Server ${selectedServer.name} with id ${selectedServer.id} was successfully deleted`;
                toast.success(<ToastContent type="success" message={message} />, {
                    position: 'top-right',
                });

                const index = edcServersState.indexOf(selectedServer);
                if (index > -1) {
                    let newServers = edcServersState;
                    newServers.splice(index, 1);
                    setEdcServersState(newServers);
                }
            })
            .catch(error => {
                toast.error(<ToastContent type="error" message="An error occurred" />, {
                    position: 'top-center',
                });
            });

        closeAllModals();
    };

    const serverRows = useMemo(
        () =>
            edcServersState.map((edcServer, index) => ({
                id: <CellText>{edcServer.id}</CellText>,
                name: <CellText>{edcServer.name}</CellText>,
                url: <CellText>{edcServer.url}</CellText>,
                flag: <CellText>{edcServer.flag}</CellText>,
                defaultServer: <CellText>{edcServer.default ? 'Yes' : 'No'}</CellText>,
                menu: (
                    <ActionsCell
                        items={[
                            { destination: () => openUpdateModal(edcServer), label: 'Edit server' },
                            { destination: () => handleDeleteConfirm(edcServer), label: 'Remove server' },
                        ]}
                    />
                ),
            })) || [],
        [edcServersState]
    );

    return (
        <div>
            {isLoading && <LoadingOverlay accessibleLabel="Loading EDC servers information" />}

            <AddEDCServerModal open={showAddModal} onClose={closeAllModals} handleSave={handleNewServer} />
            <UpdateEDCServerModal open={showUpdateModal} onClose={closeAllModals} handleSave={handleUpdate} data={selectedServer} />

            <ConfirmModal
                title="Remove server"
                action="Remove server"
                variant="primary"
                onConfirm={handleDelete}
                onCancel={closeAllModals}
                show={showRemoveModal}
            >
                Are you sure you want remove <strong>{selectedServer && selectedServer.name}</strong> from the server list?
            </ConfirmModal>

            <Stack distribution="trailing">
                <Button
                    icon="add"
                    onClick={() => {
                        openAddModal();
                    }}
                >
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
                        width: 34,
                    },
                ]}
            />
        </div>
    );
};

export { EDCServersGrid };
