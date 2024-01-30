import React, { Component } from 'react';
import { Button, CellText, DataGrid, Stack } from '@castoredc/matter';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import DataSpecificationVersionModal from 'modals/DataSpecificationVersionModal';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType, ucfirst } from '../../../util';

interface VersionsProps extends AuthorizedRouteComponentProps {
    type: string;
    getDataSpecification: () => void;
    dataSpecification: any;
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
        const { type, dataSpecification, getDataSpecification } = this.props;

        apiClient
            .post('/api/' + type + '/' + dataSpecification.id + '/v', {
                type: version,
            })
            .then(response => {
                toast.success(<ToastItem type="success" title="A new version was successfully created" />, {
                    position: 'top-right',
                });

                this.closeModal();

                getDataSpecification();
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while creating a new version';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    render() {
        const { showModal } = this.state;
        const { type, dataSpecification } = this.props;

        const latestVersion = dataSpecification.versions.slice(-1)[0].version;

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

        const rows = dataSpecification.versions.map(version => {
            return {
                version: <CellText>{version.version}</CellText>,
                moduleCount: <CellText>{version.count.modules}</CellText>,
                nodeCount: <CellText>{version.count.nodes}</CellText>,
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
                    <Stack distribution="trailing" alignment="end">
                        <Button icon="add" onClick={this.openModal}>
                            Create version
                        </Button>
                    </Stack>
                </div>

                <DataGrid
                    accessibleName={`${ucfirst(getType(type))} versions`}
                    emptyStateContent={`This ${getType(type)} does not have any versions`}
                    rows={rows}
                    columns={columns}
                />
            </PageBody>
        );
    }
}
