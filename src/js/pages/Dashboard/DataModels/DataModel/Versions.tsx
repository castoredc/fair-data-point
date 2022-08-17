import React, { Component } from 'react';
import { Button, CellText, DataGrid, Stack } from '@castoredc/matter';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import DataModelVersionModal from '../../../../modals/DataModelVersionModal';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';

interface VersionsProps extends AuthorizedRouteComponentProps {
    getDataModel: () => void;
    dataModel: any;
}

interface VersionsState {
    showModal: boolean;
}

export default class Versions extends Component<VersionsProps, VersionsState> {
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
        const { dataModel, getDataModel } = this.props;

        apiClient
            .post('/api/model/' + dataModel.id + '/v', {
                type: version,
            })
            .then(response => {
                toast.success(<ToastContent type="success" message="A new version was successfully created" />, {
                    position: 'top-right',
                });

                this.closeModal();

                getDataModel();
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while creating a new version';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { showModal } = this.state;
        const { dataModel } = this.props;

        const latestVersion = dataModel.versions.slice(-1)[0].version;

        const columns = [
            {
                Header: 'Version',
                accessor: 'version',
            },
            {
                Header: 'Groups',
                accessor: 'moduleCount',
            },
            {
                Header: 'Nodes',
                accessor: 'nodeCount',
            },
        ];

        const rows = dataModel.versions.map(version => {
            return {
                version: <CellText>{version.version}</CellText>,
                moduleCount: <CellText>{version.count.modules}</CellText>,
                nodeCount: <CellText>{version.count.nodes}</CellText>,
            };
        });

        return (
            <PageBody>
                <DataModelVersionModal
                    show={showModal}
                    latestVersion={latestVersion}
                    handleClose={() => {
                        this.closeModal();
                    }}
                    handleSave={this.createNewVersion}
                />

                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button icon="add" onClick={this.openModal}>
                            Create version
                        </Button>
                    </Stack>
                </div>

                <DataGrid
                    accessibleName="Data model versions"
                    emptyStateContent="This data model does not have any versions"
                    rows={rows}
                    columns={columns}
                />
            </PageBody>
        );
    }
}
