import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import CSVStudyStructure from "../../../components/StudyStructure/CSVStudyStructure";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DataModelMappingsDataTable from "../../../components/DataTable/DataModelMappingsDataTable";
import AddCatalogModal from "../../../modals/AddCatalogModal";
import DataModelMappingModal from "../../../modals/DataModelMappingModal";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";

export default class DistributionContentsRdf extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:    false,
            selectedMapping: null,
            addedMapping: null
        };
    }

    openModal = (mapping) => {
        this.setState({
            showModal: true,
            selectedMapping: mapping,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    onSave = (mapping) => {
        this.setState({
            showModal: false,
            addedMapping: mapping,
        });
    };

    render() {
        const {showModal, selectedMapping, addedMapping} = this.state;
        const {distribution, dataset} = this.props;

        return <div className="PageContainer">
            <DataModelMappingModal
                dataset={dataset}
                distribution={distribution}
                studyId={distribution.study.id}
                show={showModal}
                handleClose={this.closeModal}
                mapping={selectedMapping}
                onSave={this.onSave}
            />

            <DataModelMappingsDataTable
                dataset={dataset}
                distribution={distribution}
                onClick={this.openModal}
                lastHandledMapping={addedMapping}
            />
        </div>;
    }
}