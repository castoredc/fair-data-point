import React, { Component } from 'react';
import { ActionsCell, Button, CellText, DataGrid, LoadingOverlay, Stack } from '@castoredc/matter';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import {PermissionOptionType, PermissionType} from 'types/PermissionType';
import Avatar from 'react-avatar';
import AddUserModal from 'modals/AddUserModal';
import ConfirmModal from 'modals/ConfirmModal';
import { UserType } from 'types/UserType';
import { apiClient } from 'src/js/network';
import {Permissions} from "components/PermissionEditor/Permissions";

interface PermissionEditorProps {
    user: UserType | null;
    getObject: () => void;
    object: any;
    type: string;
    permissions: PermissionOptionType[],
}

interface PermissionEditorState {
    showModal: any;
    isLoading: boolean;
    assignedPermissions: PermissionType[];
    modalData: any;
}

export default class PermissionEditor extends Component<PermissionEditorProps, PermissionEditorState> {
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
        const { object, type } = this.props;

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
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { object, type } = this.props;
        const { modalData } = this.state;

        apiClient
            .post('/api/permissions/' + type + '/' + object.id + (modalData !== null ? '/' + modalData.user.id : ''), values)
            .then(response => {
                setSubmitting(false);

                toast.success(<ToastContent type="success" message={`${response.data.user.name}'s permissions were successfully set`} />, {
                    position: 'top-right',
                });

                this.closeModal('add');
                this.getPermissions();
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    handleRevoke = () => {
        const { object, type } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete('/api/permissions/' + type + '/' + object.id + '/' + modalData.user.id)
            .then(() => {
                toast.success(<ToastContent type="success" message={`${modalData.user.name}'s permissions were successfully revoked`} />, {
                    position: 'top-right',
                });

                this.closeModal('remove');
                this.getPermissions();
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    render() {
        const { showModal, assignedPermissions, isLoading, modalData } = this.state;
        const { type, permissions } = this.props;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading users" />;
        }

        const columns = [
            {
                Header: 'Name',
                accessor: 'name',
                width: 280,
            },
            {
                Header: 'Permission',
                accessor: 'type',
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
        ];

        const rows = assignedPermissions.map(permission => {
            return {
                name: (
                    <CellText>
                        <Stack wrap={false}>
                            <Avatar name={permission.user.name} size="35px" round />
                            <span>{permission.user.name}</span>
                        </Stack>
                    </CellText>
                ),
                type: <CellText>{Permissions[permission.type].labelText}</CellText>,
                menu: (
                    <ActionsCell
                        items={[
                            {
                                destination: () => {
                                    this.openModal('add', permission);
                                },
                                label: 'Edit permissions',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', permission);
                                },
                                label: 'Revoke permissions',
                            },
                        ]}
                    />
                ),
            };
        });

        return (
            <div className="PageBody">
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
                        variant="danger"
                        onConfirm={this.handleRevoke}
                        onCancel={() => this.closeModal('remove')}
                        show={showModal.remove}
                    >
                        Are you sure you want to revoke permissions for <strong>{modalData.user.name}</strong>?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button icon="add" onClick={() => this.openModal('add', null)}>
                            Add user
                        </Button>
                    </Stack>
                </div>

                <DataGrid
                    accessibleName="Permissions"
                    emptyStateContent={`This ${type} does not have any users added to it`}
                    rows={rows}
                    columns={columns}
                    anchorRightColumns={1}
                />
            </div>
        );
    }
}
