import React, {Component} from "react";
import DataModelMappingsDataTable from "../../../components/DataTable/DataModelMappingsDataTable";
import DataModelMappingModal from "../../../modals/DataModelMappingModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Dropdown as CastorDropdown} from "@castoredc/matter";
import FormItem from "../../../components/Form/FormItem";

export default class DistributionContentsRdf extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:    false,
            selectedMapping: null,
            addedMapping: null,
            isLoadingDataModel: true,
            hasLoadedDataModel: false,
            currentVersion: null,
            dataModel: null
        };
    }

    componentDidMount() {
        this.getDataModel();
    }

    getDataModel = () => {
        const { distribution } = this.props;

        this.setState({
            isLoadingDataModel: true,
        });

        axios.get('/api/model/' + distribution.dataModel.dataModel )
            .then((response) => {
                this.setState({
                    dataModel:          response.data,
                    isLoadingDataModel: false,
                    hasLoadedDataModel: true,
                    currentVersion:     distribution.dataModel.id
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataModel: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data model';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

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

    handleVersionChange = (version) => {
        this.setState({
            currentVersion: version,
        });
    };

    render() {
        const {showModal, selectedMapping, addedMapping, isLoadingDataModel, dataModel, currentVersion} = this.state;
        const {distribution, dataset} = this.props;

        if (isLoadingDataModel) {
            return <InlineLoader/>;
        }

        const versions = dataModel.versions.map((version) => {
            return {value: version.id, label: version.version};
        });

        return <div className="PageContainer">
            <DataModelMappingModal
                dataset={dataset}
                distribution={distribution}
                studyId={distribution.study.id}
                show={showModal}
                handleClose={this.closeModal}
                mapping={selectedMapping}
                onSave={this.onSave}
                versionId={currentVersion}
            />

            <FormItem label="Data model version">
                <CastorDropdown
                    onChange={(e) => {this.handleVersionChange(e.value)}}
                    value={versions.find(({value}) => value === currentVersion)}
                    options={versions}
                    menuPlacement="auto"
                    width="minimum"
                />
            </FormItem>

            <DataModelMappingsDataTable
                dataset={dataset}
                distribution={distribution}
                onClick={this.openModal}
                lastHandledMapping={addedMapping}
                versionId={currentVersion}
            />
        </div>;
    }
}