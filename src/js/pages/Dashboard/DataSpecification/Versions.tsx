import React, { Component } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import DataSpecificationVersionModal from 'modals/DataSpecificationVersionModal';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';
import DataGrid from 'components/DataTable/DataGrid';
import Stack from '@mui/material/Stack';
import { GridColDef } from '@mui/x-data-grid';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface VersionsProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
    getDataSpecification: () => void;
    dataSpecification: any;
}

interface VersionsState {
    showModal: boolean;
}

class Versions extends Component<VersionsProps, VersionsState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
        };
    }

    openModal = () => {
        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    createNewVersion = version => {
        const { type, dataSpecification, getDataSpecification, notifications } = this.props;

        apiClient
            .post('/api/' + type + '/' + dataSpecification.id + '/v', {
                type: version,
            })
            .then(response => {
                notifications.show('A new version was successfully created', {
                    variant: 'success',

                });

                this.closeModal();

                getDataSpecification();
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while creating a new version';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { showModal } = this.state;
        const { type, dataSpecification } = this.props;

        const latestVersion = dataSpecification.versions.slice(-1)[0].version;

        const columns: GridColDef[] = [
            {
                headerName: 'Version',
                field: 'version',
            },
            {
                headerName: 'Groups',
                field: 'moduleCount',
            },
            {
                headerName: 'Nodes',
                field: 'nodeCount',
            },
        ];

        const rows = dataSpecification.versions.map(version => {
            return {
                id: version.id,
                version: version.version,
                moduleCount: version.count.modules,
                nodeCount: version.count.nodes,
            };
        });

        return (
            <PageBody>
                <DataSpecificationVersionModal
                    type={type}
                    show={showModal}
                    latestVersion={latestVersion}
                    handleClose={() => {
                        this.closeModal();
                    }}
                    handleSave={this.createNewVersion}
                />

                <div className="PageButtons">
                    <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                        <Button startIcon={<AddIcon />} onClick={this.openModal}>
                            Create version
                        </Button>
                    </Stack>
                </div>

                <DataGrid
                    disableRowSelectionOnClick
                    emptyStateContent={`This ${getType(type)} does not have any versions`}
                    rows={rows}
                    columns={columns}
                />
            </PageBody>
        );
    }
}

export default withNotifications(Versions);