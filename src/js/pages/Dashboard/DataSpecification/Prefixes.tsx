import React, { Component } from 'react';
import DataSpecificationPrefixModal from 'modals/DataSpecificationPrefixModal';
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

interface PrefixesProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
    prefixes: any;
    getPrefixes: () => void;
    dataSpecification: any;
    version: any;
}

interface PrefixesState {
    showModal: any;
    prefixModalData: any;
}

class Prefixes extends Component<PrefixesProps, PrefixesState> {
    private tableRef: React.RefObject<unknown>;

    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            prefixModalData: null,
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
            prefixModalData: data,
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
        const { getPrefixes } = this.props;
        this.closeModal(type);
        getPrefixes();
    };

    removePrefix = () => {
        const { type, dataSpecification, version, notifications } = this.props;
        const { prefixModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/prefix/' + prefixModalData.id)
            .then(() => {
                notifications.show(`The prefix was successfully removed`, {
                    variant: 'success',

                });

                this.onSaved('remove');
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    render() {
        const { showModal, prefixModalData } = this.state;
        const { type, dataSpecification, prefixes, version } = this.props;

        const columns: GridColDef[] = [
            {
                headerName: 'Prefix',
                field: 'prefix',
            },
            {
                headerName: 'URI',
                field: 'uri',
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
                                label: 'Edit prefix',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', params.row.data);
                                },
                                label: 'Delete prefix',
                            },
                        ]}
                    />;
                },
            },
        ];

        const rows = prefixes.map(item => {
            const data = {
                id: item.id,
                prefix: item.prefix,
                uri: item.uri,
            };

            return {
                id: item.id,
                prefix: item.prefix,
                uri: item.uri,
                data: data,
            };
        });

        return (
            <PageBody>
                <DataSpecificationPrefixModal
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
                    data={prefixModalData}
                />

                {prefixModalData && (
                    <ConfirmModal
                        title="Delete prefix"
                        action="Delete prefix"
                        variant="contained"
                        color="error"
                        onConfirm={this.removePrefix}
                        onCancel={() => {
                            this.closeModal('remove');
                        }}
                        show={showModal.remove}
                    >
                        Are you sure you want to delete prefix <strong>{prefixModalData.prefix}</strong>?
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
                            Add prefix
                        </Button>
                    </Stack>
                </div>

                <DataGridContainer fullHeight forwardRef={this.tableRef}>
                    <DataGrid
                        disableRowSelectionOnClick
                        accessibleName="Prefixes"
                        // anchorRightColumns={1}
                        emptyStateContent={`This ${getType(type)} does not have prefixes`}
                        rows={rows}
                        columns={columns}
                    />
                </DataGridContainer>
            </PageBody>
        );
    }
}

export default withNotifications(Prefixes);