import React, { Component } from 'react';
import ConfirmModal from '../../../../modals/ConfirmModal';
import DatasetsDataTable from 'components/DataTable/DatasetsDataTable';
import { localizedText } from '../../../../util';
import * as H from 'history';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface AddDatasetProps extends ComponentWithNotifications {
    catalog: string;
    history: H.History;
}

interface AddDatasetState {
    showModal: any;
    selectedDataset: any;
    addedDataset: any;
}

class AddDataset extends Component<AddDatasetProps, AddDatasetState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                newDataset: false,
                confirm: false,
            },
            selectedDataset: null,
            addedDataset: null,
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

    closeModal = type => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    handleDatasetClick = dataset => {
        this.setState(
            {
                selectedDataset: dataset,
            },
            () => {
                this.openModal('confirm');
            },
        );
    };

    handleAdd = () => {
        const { catalog, notifications } = this.props;
        const { selectedDataset } = this.state;

        apiClient
            .post('/api/catalog/' + catalog + '/dataset/add', {
                datasetId: selectedDataset.id,
            })
            .then(response => {
                notifications.show('The dataset was successfully added to the catalog', {
                    variant: 'success',

                });

                this.closeModal('confirm');

                this.setState({
                    addedDataset: selectedDataset,
                });
            })
            .catch(error => {
                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while adding the dataset to the catalog';
                notifications.show(message, { variant: 'error' });
            });
    };

    render() {
        const { catalog } = this.props;
        const { showModal, selectedDataset, addedDataset } = this.state;

        return (
            <PageBody>
                {selectedDataset && (
                    <ConfirmModal
                        title="Add dataset"
                        action="Add dataset"
                        variant="contained"
                        onConfirm={this.handleAdd}
                        onCancel={() => {
                            this.closeModal('confirm');
                        }}
                        show={showModal.confirm}
                    >
                        Are you sure you want to add{' '}
                        {selectedDataset.hasMetadata ?
                            <strong>{localizedText(selectedDataset.metadata.title, 'en')}</strong> : 'this dataset'} to
                        this catalog?
                    </ConfirmModal>
                )}

                <DatasetsDataTable onClick={this.handleDatasetClick} hideCatalog={catalog}
                                   lastHandledDataset={addedDataset} />
            </PageBody>
        );
    }
}

export default withNotifications(AddDataset);