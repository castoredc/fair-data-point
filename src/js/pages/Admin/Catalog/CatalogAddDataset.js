import React, {Component} from "react";
import ConfirmModal from "../../../modals/ConfirmModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";
import {localizedText} from "../../../util";

export default class CatalogAddDataset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                newDataset: false,
                confirm: false
            },
            selectedDataset: null,
            addedDataset: null
        };
    }

    openModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal:       {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    handleDatasetClick = (dataset) => {
        this.setState({
            selectedDataset: dataset,
        }, () => {
            this.openModal('confirm');
        })
    };

    handleAdd = () => {
        const { catalog } = this.props;
        const { selectedDataset } = this.state;

        axios.post('/api/catalog/' + catalog.slug + '/dataset/add', {
            datasetId: selectedDataset.id
        })
            .then((response) => {
                toast.success(<ToastContent type="success" message="The dataset was successfully added to the catalog" />, {
                    position: "top-right"
                });

                this.closeModal('confirm');

                this.setState({
                    addedDataset: selectedDataset,
                });

            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while adding the dataset to the catalog';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { catalog } = this.props;
        const { showModal, selectedDataset, addedDataset } = this.state;

        return <div className="PageBody">
            {selectedDataset && <ConfirmModal
                title="Add dataset"
                action="Add dataset"
                variant="primary"
                onConfirm={this.handleAdd}
                onCancel={() => {this.closeModal('confirm')}}
                show={showModal.confirm}
            >
                Are you sure you want to add {selectedDataset.hasMetadata ? <strong>{localizedText(selectedDataset.metadata.title, 'en')}</strong> : 'this dataset'} to this catalog?
            </ConfirmModal>}

            <DatasetsDataTable
                onClick={this.handleDatasetClick}
                hideCatalog={catalog}
                lastHandledDataset={addedDataset}
            />
        </div>;
    }
}