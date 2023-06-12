import React, { Component } from 'react';
import { ActionsCell, Button, CellText, DataGrid, Stack } from '@castoredc/matter';
import { ucfirst } from '../../../../util';
import PublisherModal from '../../../../modals/PublisherModal';
import ConfirmModal from '../../../../modals/ConfirmModal';
import { toast } from 'react-toastify';
import {ToastMessage} from '@castoredc/matter';

type PublishersMetadataProps = {
    languages: any;
    licenses: any;
    countries: any;
    validation: any;
    publishers: any;
    setValue: (field: string, value: any, shouldValidate?: boolean) => void;
    type: string;
};

type PublishersMetadataState = {
    showModal: boolean;
    showRemoveModal: boolean;
    selectedPublisher: any | null;
};

export default class PublishersMetadata extends Component<PublishersMetadataProps, PublishersMetadataState> {
    constructor(props) {
        super(props);

        this.state = {
            showModal: false,
            showRemoveModal: false,
            selectedPublisher: null,
        };
    }

    openModal = id => {
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

    handleUpdate = newPublisher => {
        const { publishers, setValue } = this.props;

        const exists =
            newPublisher.id !== ''
                ? !!publishers.find(publisher => {
                      if (publisher.type !== newPublisher.type) {
                          return false;
                      }

                      return publisher[publisher.type].id === newPublisher[newPublisher.type].id;
                  })
                : false;
        if (!exists) {
            let newPublishers = publishers;
            newPublishers.push(newPublisher);

            setValue('publishers', newPublishers);
        } else {
            toast.error(
                <ToastMessage type="error" title="The publisher was already associated with this metadata and was, therefore, not added again." />
            );
        }

        this.closeModal();
    };

    handleDeleteConfirm = publisher => {
        this.setState({
            showRemoveModal: true,
            selectedPublisher: publisher,
        });
    };

    handleDelete = () => {
        const { publishers, setValue } = this.props;
        const { selectedPublisher } = this.state;

        const index = publishers.indexOf(selectedPublisher);

        if (index > -1) {
            let newPublishers = publishers;
            newPublishers.splice(index, 1);

            setValue('publishers', newPublishers);
        }

        this.closeModal();
    };

    render() {
        const { countries, publishers, type } = this.props;
        const { showModal, showRemoveModal, selectedPublisher } = this.state;

        const publisherRows = publishers.map((publisher, index) => {
            let name = '';
            let additionalInfo = '';

            if (publisher.type === 'organization') {
                name = publisher.organization.name;
                additionalInfo = typeof publisher.department !== 'undefined' ? publisher.department.name : '';
            } else if (publisher.type === 'person') {
                name = [publisher.person.firstName, publisher.person.middleName, publisher.person.lastName].filter(Boolean).join(' ');
                additionalInfo = publisher.person.orcid;
            }

            return {
                title: <CellText>{name}</CellText>,
                type: <CellText>{ucfirst(publisher.type)}</CellText>,
                info: <CellText>{additionalInfo}</CellText>,
                menu: <ActionsCell items={[{ destination: () => this.handleDeleteConfirm(publisher), label: 'Remove publisher' }]} />,
            };
        });

        let selectedPublisherName = '';
        if (selectedPublisher !== null) {
            if (selectedPublisher.type === 'organization') {
                selectedPublisherName = selectedPublisher.organization.name;
            } else if (selectedPublisher.type === 'person') {
                selectedPublisherName = [selectedPublisher.person.firstName, selectedPublisher.person.middleName, selectedPublisher.person.lastName]
                    .filter(Boolean)
                    .join(' ');
            }
        }

        return (
            <div>
                <PublisherModal open={showModal} onClose={this.closeModal} handleSave={this.handleUpdate} countries={countries} />

                <ConfirmModal
                    title="Remove publisher"
                    action="Remove publisher"
                    variant="primary"
                    onConfirm={this.handleDelete}
                    onCancel={this.closeModal}
                    show={showRemoveModal}
                >
                    Are you sure you want remove <strong>{selectedPublisher && selectedPublisherName}</strong> as publisher?
                </ConfirmModal>

                <Stack distribution="space-between">
                    <div className="FormItemDetails">
                        Publishers are the entities (person or organization) responsible for the {type}.
                    </div>
                    <Button
                        icon="add"
                        onClick={() => {
                            this.openModal(null);
                        }}
                    >
                        Add publisher
                    </Button>
                </Stack>

                <DataGrid
                    accessibleName="Publishers"
                    emptyStateContent="No publishers found"
                    rows={publisherRows}
                    anchorRightColumns={1}
                    columns={[
                        {
                            Header: 'Name',
                            accessor: 'title',
                        },
                        {
                            Header: 'Type',
                            accessor: 'type',
                        },
                        {
                            Header: 'Additional Information',
                            accessor: 'info',
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
            </div>
        );
    }
}
