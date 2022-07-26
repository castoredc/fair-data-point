import React, {Component} from 'react';
import {ActionsCell, Button, CellText, DataGrid, Stack} from "@castoredc/matter";
import {ServerType} from "types/ServerType";
import ConfirmModal from "modals/ConfirmModal";

type EDCServersGridProps = {
    edcServers: ServerType[],
}

type EDCServersGridState = {
    showModal: boolean,
    showRemoveModal: boolean,
    edcServers: ServerType[],
    selectedServer: ServerType | null;
}

export default class EDCServersGrid extends Component<EDCServersGridProps, EDCServersGridState> {
    constructor(props) {
        super(props);

        this.state = {
            showModal: false,
            showRemoveModal: false,
            edcServers: props.edcServers,
            selectedServer: null,
        };
    }

    openModal = (id) => {
        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
            showRemoveModal: false,
        });
    };

    // handleUpdate = (newPublisher) => {
    //     const {publishers, setValue} = this.props;
    //
    //     const exists = newPublisher.id !== '' ? !!publishers.find((publisher) => {
    //         if (publisher.type !== newPublisher.type) {
    //             return false;
    //         }
    //
    //         return publisher[publisher.type].id === newPublisher[newPublisher.type].id
    //     }) : false;
    //     if (!exists) {
    //         let newPublishers = publishers;
    //         newPublishers.push(newPublisher);
    //
    //         setValue('publishers', newPublishers);
    //     } else {
    //         toast.error(<ToastContent type="error"
    //                                   message="The publisher was already associated with this metadata and was, therefore, not added again."/>);
    //     }
    //
    //     this.closeModal();
    // };

    handleDeleteConfirm = (edcServer) => {
        this.setState({
            showRemoveModal: true,
            selectedServer: edcServer
        });
    }

    handleDelete = () => {
        const {edcServers, selectedServer} = this.state;
        if (! selectedServer) {
            return;
        }

        const index = edcServers.indexOf(selectedServer);

        if (index > -1) {
            let newServers = edcServers;
            newServers.splice(index, 1);

            this.setState({
                edcServers: newServers,
            })
        }

        this.closeModal();
    };

    render() {
        const { edcServers, selectedServer, showRemoveModal } = this.state;

        const serverRows = edcServers.map((edcServer, index) => {
            return {
                name: <CellText>{edcServer.name}</CellText>,
                url: <CellText>{(edcServer.url)}</CellText>,
                flag: <CellText>{edcServer.flag}</CellText>,
                defaultServer: <CellText>{edcServer.default ? 'Yes' : 'No'}</CellText>,
                menu: <ActionsCell
                    items={[{destination: () => this.handleDeleteConfirm(edcServer), label: 'Remove server'}]}/>,
            }
        });

        return <div>
            {/*<PublisherModal*/}
            {/*    open={showModal}*/}
            {/*    onClose={this.closeModal}*/}
            {/*    handleSave={this.handleUpdate}*/}
            {/*    countries={countries}*/}
            {/*/>*/}

            <ConfirmModal
                title="Remove server"
                action="Remove server"
                variant="primary"
                onConfirm={this.handleDelete}
                onCancel={this.closeModal}
                show={showRemoveModal}
            >
                Are you sure you want remove <strong>{selectedServer && selectedServer.name}</strong> from the server list?
            </ConfirmModal>

            <Stack distribution="trailing">
                <Button icon="add" onClick={() => {
                    this.openModal(null)
                }}>
                    Add server
                </Button>
            </Stack>

            <DataGrid
                accessibleName="EDC Servers"
                emptyStateContent="No servers available"
                rows={serverRows}
                anchorRightColumns={1}
                columns={[
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
                        width: 34
                    }
                ]}
            />
        </div>;
    }
}
