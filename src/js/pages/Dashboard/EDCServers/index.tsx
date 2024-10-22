import React, { Component } from 'react';
import DocumentTitle from 'components/DocumentTitle';
import DashboardTab from 'components/Layout/DashboardTab';
import DashboardTabHeader from 'components/Layout/DashboardTab/DashboardTabHeader';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from '../../../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { ActionsCell, Button, CellText, DataGrid, LoadingOverlay } from '@castoredc/matter';
import { isAdmin } from 'utils/PermissionHelper';
import { AddEDCServerModal } from 'modals/AddEDCServerModal';
import { UpdateEDCServerModal } from 'modals/UpdateEDCServerModal';
import ConfirmModal from 'modals/ConfirmModal';

interface EDCServersProps extends AuthorizedRouteComponentProps {}

interface EDCServersState {
    edcServers: any;
    showModal: any;
    selectedServer: any;
    isLoading: boolean;
}

export default class EDCServers extends Component<EDCServersProps, EDCServersState> {
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
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred while loading the EDC Servers information" />);
                }
            });
    };

    handleDelete = () => {
        const { selectedServer } = this.state;

        apiClient
            .delete('/api/castor/servers/' + selectedServer.id)
            .then(response => {
                const message = `The EDC Server ${selectedServer.name} with id ${selectedServer.id} was successfully deleted`;
                toast.success(<ToastItem type="success" title={message} />, {
                    position: 'top-right',
                });

                this.getEDCServers();
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
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
        const { history, user } = this.props;
        const { isLoading, selectedServer, edcServers, showModal } = this.state;

        const serverRows = edcServers.map((edcServer, index) => ({
            id: <CellText>{edcServer.id}</CellText>,
            name: <CellText>{edcServer.name}</CellText>,
            url: <CellText>{edcServer.url}</CellText>,
            flag: <CellText>{edcServer.flag}</CellText>,
            defaultServer: <CellText>{edcServer.default ? 'Yes' : 'No'}</CellText>,
            menu: (
                <ActionsCell
                    items={[
                        { destination: () => this.openServerModal('update', edcServer), label: 'Edit server' },
                        { destination: () => this.openServerModal('remove', edcServer), label: 'Remove server' },
                    ]}
                />
            ),
        }));

        return (
            <DashboardTab>
                <AddEDCServerModal open={showModal.add} onClose={this.closeModals} handleSave={this.handleUpdate} />
                <UpdateEDCServerModal open={showModal.update} onClose={this.closeModals} handleSave={this.handleUpdate} data={selectedServer} />

                <ConfirmModal
                    title="Remove server"
                    action="Remove server"
                    variant="primary"
                    onConfirm={this.handleDelete}
                    onCancel={this.closeModals}
                    show={showModal.remove}
                >
                    Are you sure you want remove <strong>{selectedServer && selectedServer.name}</strong> from the server list?
                </ConfirmModal>

                <DocumentTitle title="EDC Servers overview" />

                {isLoading && <LoadingOverlay accessibleLabel="Loading servers" />}

                <DashboardTabHeader title="EDC Servers overview" type="Section">
                    {isAdmin(user) && (
                        <Button buttonType="primary" onClick={() => this.openModal('add')}>
                            Add new server
                        </Button>
                    )}
                </DashboardTabHeader>

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
            </DashboardTab>
        );
    }
}
