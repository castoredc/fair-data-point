import React, { Component } from 'react';
import DocumentTitle from 'components/DocumentTitle';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from '../../../network';
import { isAdmin } from 'utils/PermissionHelper';
import { AddEDCServerModal } from 'modals/AddEDCServerModal';
import { UpdateEDCServerModal } from 'modals/UpdateEDCServerModal';
import ConfirmModal from 'modals/ConfirmModal';
import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import DashboardPage from 'components/Layout/Dashboard/DashboardPage';
import DashboardSideBar from 'components/SideBar/DashboardSideBar';
import Body from 'components/Layout/Dashboard/Body';
import DataGrid from 'components/DataTable/DataGrid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import Header from 'components/Layout/Dashboard/Header';
import PageBody from 'components/Layout/Dashboard/PageBody';
import AddIcon from '@mui/icons-material/Add';

interface EDCServersProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface EDCServersState {
    edcServers: any;
    showModal: any;
    selectedServer: any;
    isLoading: boolean;
}

class EDCServers extends Component<EDCServersProps, EDCServersState> {
    constructor(props) {
        super(props);

        this.state = {
            edcServers: [],
            showModal: {
                add: false,
                update: false,
                remove: false,
            },
            selectedServer: null,
            isLoading: false,
        };
    }

    openModal = type => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModals = () => {
        this.setState({
            showModal: {
                add: false,
                update: false,
                remove: false,
            },
        });
    };

    getEDCServers = () => {
        const { notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/castor/servers')
            .then(response => {
                this.setState({
                    isLoading: false,
                    edcServers: response.data,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred while loading the EDC Servers information', { variant: 'error' });
                }
            });
    };

    handleDelete = () => {
        const { notifications } = this.props;
        const { selectedServer } = this.state;

        apiClient
            .delete('/api/castor/servers/' + selectedServer.id)
            .then(response => {
                const message = `The EDC Server ${selectedServer.name} with id ${selectedServer.id} was successfully deleted`;
                notifications.show(message, {
                    variant: 'success',

                });

                this.getEDCServers();
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });

        this.closeModals();
    };

    handleUpdate = () => {
        this.getEDCServers();
        this.closeModals();
    };

    openServerModal = (type, existingServer) => {
        this.openModal(type);
        this.setState({
            selectedServer: existingServer,
        });
    };

    componentDidMount() {
        this.getEDCServers();
    }

    render() {
        const { location, history, user } = this.props;
        const { isLoading, selectedServer, edcServers, showModal } = this.state;

        const serverRows = edcServers.map((edcServer, index) => ({
            id: edcServer.id,
            name: edcServer.name,
            url: edcServer.url,
            flag: edcServer.flag,
            defaultServer: edcServer.default ? 'Yes' : '/., mNo',
            data: edcServer,
        }));

        return (
            <DashboardPage>
                <AddEDCServerModal open={showModal.add} onClose={this.closeModals} handleSave={this.handleUpdate} />
                <UpdateEDCServerModal open={showModal.update} onClose={this.closeModals} handleSave={this.handleUpdate}
                                      data={selectedServer} />

                <ConfirmModal
                    title="Remove server"
                    action="Remove server"
                    variant="contained"
                    onConfirm={this.handleDelete}
                    onCancel={this.closeModals}
                    show={showModal.remove}
                >
                    Are you sure you want remove <strong>{selectedServer && selectedServer.name}</strong> from the
                    server list?
                </ConfirmModal>

                <DocumentTitle title="EDC Servers overview" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading servers" />}

                <DashboardSideBar location={location} history={history} user={user} />

                <Body>
                    <Header title="EDC Servers overview">
                        {isAdmin(user) && (
                            <Button
                                startIcon={<AddIcon />}
                                onClick={() => this.openModal('add')}
                                variant="contained"
                            >
                                Add new server
                            </Button>
                        )}
                    </Header>

                    <PageBody>
                        <DataGrid
                            disableRowSelectionOnClick
                            accessibleName="EDC Servers"
                            emptyStateContent="No servers available"
                            rows={serverRows}
                            // anchorRightColumns={1}
                            columns={[
                                {
                                    headerName: 'ID',
                                    field: 'id',
                                    width: 40,
                                    maxWidth: 40,
                                },
                                {
                                    headerName: 'Name',
                                    field: 'name',
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
                                    field: 'defaultServer',
                                    width: 150,
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
                                                    destination: () => this.openServerModal('update', params.row.data),
                                                    label: 'Edit server',
                                                },
                                                {
                                                    destination: () => this.openServerModal('remove', params.row.data),
                                                    label: 'Remove server',
                                                },
                                            ]}
                                        />;
                                    },
                                },
                            ]}
                        />
                    </PageBody>
                </Body>
            </DashboardPage>
        );
    }
}

export default withNotifications(EDCServers);