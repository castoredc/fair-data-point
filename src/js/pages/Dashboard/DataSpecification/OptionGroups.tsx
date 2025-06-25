import React, { Component } from 'react';
import OptionGroupModal from 'modals/DataSpecification/OptionGroupModal';
import ConfirmModal from 'modals/ConfirmModal';
import DataGridContainer from 'components/DataTable/DataGridContainer';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import Stack from '@mui/material/Stack';
import DataGrid from 'components/DataTable/DataGrid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import { GridColDef } from '@mui/x-data-grid';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface OptionGroupsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
    optionGroups: any;
    getOptionGroups: () => void;
    dataSpecification: any;
    version: any;
}

interface OptionGroupsState {
    showModal: any;
    optionGroupModalData: any;
}

class OptionGroups extends Component<OptionGroupsProps, OptionGroupsState> {
    private tableRef: React.RefObject<unknown>;

    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            optionGroupModalData: null,
        };

        this.tableRef = React.createRef();
    }

    openModal = (type, data) => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
            optionGroupModalData: data,
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
        const { getOptionGroups } = this.props;
        this.closeModal(type);
        getOptionGroups();
    };

    removeOptionGroup = () => {
        const { type, dataSpecification, version, notifications } = this.props;
        const { optionGroupModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/option-group/' + optionGroupModalData.id)
            .then(() => {
                notifications.show(`The option group was successfully removed`, {
                    variant: 'success',

                });

                this.onSaved('remove');
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    render() {
        const { showModal, optionGroupModalData } = this.state;
        const { type, dataSpecification, optionGroups, version } = this.props;

        const columns: GridColDef[] = [
            {
                headerName: 'Name',
                field: 'title',
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
                                label: 'Edit option group',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', params.row.data);
                                },
                                label: 'Delete option group',
                            },
                        ]}
                    />;
                },
            },
        ];

        const rows = optionGroups.map(item => {
            const data = {
                id: item.id,
                title: item.title,
                description: item.description,
                options: item.options,
            };

            return {
                id: item.id,
                title: item.title,
                data: data,
            };
        });

        return (
            <PageBody>
                <OptionGroupModal
                    type={type}
                    show={showModal.add}
                    handleClose={() => {
                        this.closeModal('add');
                    }}
                    onSaved={() => {
                        this.onSaved('add');
                    }}
                    modelId={dataSpecification.id}
                    versionId={version}
                    data={optionGroupModalData}
                />

                {optionGroupModalData && (
                    <ConfirmModal
                        title="Delete option group"
                        action="Delete option group"
                        variant="contained"
                        color="error"
                        onConfirm={this.removeOptionGroup}
                        onCancel={() => {
                            this.closeModal('remove');
                        }}
                        show={showModal.remove}
                    >
                        Are you sure you want to delete option group <strong>{optionGroupModalData.title}</strong>?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => {
                                this.openModal('add', null);
                            }}
                            variant="contained"
                        >
                            Add option group
                        </Button>
                    </Stack>
                </div>

                <DataGridContainer fullHeight forwardRef={this.tableRef}>
                    <DataGrid
                        disableRowSelectionOnClick
                        accessibleName="OptionGroups"
                        // anchorRightColumns={1}
                        emptyStateContent={`This ${getType(type)} does not have option groups`}
                        rows={rows}
                        columns={columns}
                    />
                </DataGridContainer>
            </PageBody>
        );
    }
}

export default withNotifications(OptionGroups);