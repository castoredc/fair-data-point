import React, { useEffect, useState } from 'react';
import DocumentTitle from 'components/DocumentTitle';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from '../../../network';
import { isAdmin } from 'utils/PermissionHelper';
import { AddEDCServerModal } from 'modals/AddEDCServerModal';
import { UpdateEDCServerModal } from 'modals/UpdateEDCServerModal';
import ConfirmModal from 'modals/ConfirmModal';
import Button from '@mui/material/Button';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import DashboardSideBar from 'components/SideBar/DashboardSideBar';
import Body from 'components/Layout/Dashboard/Body';
import DataGrid from 'components/DataTable/DataGrid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import Header from 'components/Layout/Dashboard/Header';
import PageBody from 'components/Layout/Dashboard/PageBody';
import AddIcon from '@mui/icons-material/Add';
import { Box } from '@mui/material';
import { GridColDef } from '@mui/x-data-grid';

interface EDCServer {
    id: string;
    name: string;
    url: string;
    flag: string;
    default: boolean;
}

interface EDCServersProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {}

type ModalType = 'add' | 'update' | 'remove';

const EDCServers: React.FC<EDCServersProps> = ({ history, location, user, notifications }): JSX.Element => {
    const [edcServers, setEDCServers] = useState<EDCServer[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [selectedServer, setSelectedServer] = useState<EDCServer | null>(null);
    const [showModal, setShowModal] = useState({
        add: false,
        update: false,
        remove: false,
    });

    const openModal = (type: ModalType) => {
        setShowModal(prev => ({
            ...prev,
            [type]: true,
        }));
    };

    const closeModals = () => {
        setShowModal({
            add: false,
            update: false,
            remove: false,
        });
        setSelectedServer(null);
    };

    const getEDCServers = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await apiClient.get('/api/castor/servers');
            setEDCServers(response.data);
        } catch (error: any) {
            const errorMessage = error.response?.data?.error || 'An error occurred while loading the EDC Servers information';
            setError(errorMessage);
            notifications.show(errorMessage, { variant: 'error' });
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async () => {
        if (!selectedServer) return;

        try {
            await apiClient.delete(`/api/castor/servers/${selectedServer.id}`);
            const message = `The EDC Server ${selectedServer.name} with id ${selectedServer.id} was successfully deleted`;
            notifications.show(message, { variant: 'success' });
            await getEDCServers();
        } catch (error: any) {
            notifications.show('An error occurred', { variant: 'error' });
        }

        closeModals();
    };

    const handleUpdate = async () => {
        await getEDCServers();
        closeModals();
    };

    const openServerModal = (type: ModalType, server: EDCServer) => {
        setSelectedServer(server);
        openModal(type);
    };

    useEffect(() => {
        getEDCServers();
    }, []);

    const columns: GridColDef<EDCServer>[] = [
        {
            headerName: 'ID',
            field: 'id',
            width: 40,
            maxWidth: 40,
        },
        {
            headerName: 'Name',
            field: 'name',
            flex: 1,
        },
        {
            headerName: 'URL',
            field: 'url',
            minWidth: 200,
            width: 300,
        },
        {
            headerName: 'Location',
            field: 'flag',
            width: 100,
        },
        {
            headerName: 'Default server?',
            field: 'default',
            width: 150,
            type: 'boolean',
            valueFormatter: ({ value }) => value ? 'Yes' : 'No',
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
                const row = params.row as EDCServer;
                return (
                    <RowActionsMenu
                        row={row}
                        items={[
                            {
                                destination: () => openServerModal('update', row),
                                label: 'Edit server',
                            },
                            {
                                destination: () => openServerModal('remove', row),
                                label: 'Remove server',
                            },
                        ]}
                    />
                );
            },
        },
    ];

    return (
        <DashboardPage>
            <AddEDCServerModal 
                open={showModal.add} 
                onClose={closeModals} 
                handleSave={handleUpdate} 
            />
            <UpdateEDCServerModal 
                open={showModal.update} 
                onClose={closeModals} 
                handleSave={handleUpdate}
                data={selectedServer} 
            />

            <ConfirmModal
                title="Remove server"
                action="Remove server"
                variant="contained"
                onConfirm={handleDelete}
                onCancel={closeModals}
                show={showModal.remove}
            >
                Are you sure you want remove <strong>{selectedServer?.name}</strong> from the
                server list?
            </ConfirmModal>

            <DocumentTitle title="EDC Servers overview" />
            <DashboardSideBar location={location} history={history} user={user} />

            <Body>
                <Header title="EDC Servers overview">
                    {isAdmin(user) && (
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => openModal('add')}
                            variant="contained"
                        >
                            Add new server
                        </Button>
                    )}
                </Header>

                <PageBody>
                    <Box sx={{ height: 400, width: '100%' }}>
                        <DataGrid
                            rows={edcServers}
                            columns={columns}
                            loading={loading}
                            error={error}
                            disableRowSelectionOnClick
                            emptyStateContent="No servers available"
                            sx={{ '& .actionsCell': { pr: 1 } }}
                        />
                    </Box>
                </PageBody>
            </Body>
        </DashboardPage>
    );
};

export default withNotifications(EDCServers);