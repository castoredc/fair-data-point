import React, { Component } from 'react';
import AddIcon from '@mui/icons-material/Add';
import { PermissionOptionType, PermissionType } from 'types/PermissionType';
import AddUserModal from 'modals/AddUserModal';
import ConfirmModal from 'modals/ConfirmModal';
import { UserType } from 'types/UserType';
import { apiClient } from 'src/js/network';
import { Permissions } from 'components/PermissionEditor/Permissions';
import Button from '@mui/material/Button';
import Stack from '@mui/material/Stack';
import { Box } from '@mui/material';
import LoadingOverlay from 'components/LoadingOverlay';
import DataGrid from 'components/DataTable/DataGrid';
import { GridColDef } from '@mui/x-data-grid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface PermissionEditorProps extends ComponentWithNotifications {
    user: UserType | null;
    getObject: () => void;
    object: any;
    type: string;
    permissions: PermissionOptionType[];
}

interface PermissionEditorState {
    showModal: any;
    isLoading: boolean;
    assignedPermissions: PermissionType[];
    modalData: any;
}

class PermissionEditor extends Component<PermissionEditorProps, PermissionEditorState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            isLoading: true,
            assignedPermissions: [],
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
            modalData: null,
        });
    };

    componentDidMount() {
        this.getPermissions();
    }

    getPermissions = () => {
        const { object, type, notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/permissions/' + type + '/' + object.id)
            .then(response => {
                this.setState({
                    assignedPermissions: response.data.results,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { object, type, notifications } = this.props;
        const { modalData } = this.state;

        apiClient
            .post('/api/permissions/' + type + '/' + object.id + (modalData !== null ? '/' + modalData.user.id : ''), values)
            .then(response => {
                setSubmitting(false);

                notifications.show(`${response.data.user.name}'s permissions were successfully set`, {
                    variant: 'success',

                });

                this.closeModal('add');
                this.getPermissions();
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    handleRevoke = () => {
        const { object, type, notifications } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete('/api/permissions/' + type + '/' + object.id + '/' + modalData.user.id)
            .then(() => {
                notifications.show(`${modalData.user.name}'s permissions were successfully revoked`, {
                    variant: 'success',

                });

                this.closeModal('remove');
                this.getPermissions();
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    render() {
        const { showModal, assignedPermissions, isLoading, modalData } = this.state;
        const { type, permissions } = this.props;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading users" />;
        }

        const columns: GridColDef[] = [
            {
                headerName: 'Name',
                field: 'name',
                width: 280,
            },
            {
                headerName: 'Permission',
                field: 'type',
            },
            {
                field: 'actions',
                headerName: '',
                flex: 1,
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
                                label: 'Edit permissions',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', params.row.data);
                                },
                                label: 'Revoke permissions',
                            },
                        ]}
                    />;
                },
            },
        ];

        const rows = assignedPermissions.map(permission => {
            return {
                id: permission.user.id,
                name: permission.user.name,
                type: Permissions[permission.type].label,
                data: permission,
            };
        });

        return (
            <PageBody>
                <AddUserModal
                    open={showModal.add}
                    onClose={() => this.closeModal('add')}
                    handleSubmit={this.handleSubmit}
                    data={modalData}
                    permissions={permissions}
                />

                {modalData && (
                    <ConfirmModal
                        title="Revoke permissions"
                        action="Revoke permissions"
                        variant="contained"
                        color="error"
                        onConfirm={this.handleRevoke}
                        onCancel={() => this.closeModal('remove')}
                        show={showModal.remove}
                    >
                        Are you sure you want to revoke permissions for <strong>{modalData.user.name}</strong>?
                    </ConfirmModal>
                )}

                <Stack direction="row" sx={{ justifyContent: 'flex-end', mb: 2 }}>
                    <Button
                        startIcon={<AddIcon />}
                        onClick={() => this.openModal('add', null)}
                        variant="contained"
                    >
                        Add user
                    </Button>
                </Stack>

                <Box sx={{ height: 400, width: '100%' }}>
                    <DataGrid
                        disableRowSelectionOnClick
                        accessibleName="Permissions"
                        emptyStateContent={`There are no users added yet`}
                        rows={rows}
                        columns={columns}
                        // sx={{ '& .actionsCell': { pr: 1 } }}
                    />
                </Box>
            </PageBody>
        );
    }
}

export default withNotifications(PermissionEditor);