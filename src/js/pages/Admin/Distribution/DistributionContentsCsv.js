import React, {Component} from "react";
import axios from "axios/index";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import CSVStudyStructure from "../../../components/StudyStructure/CSVStudyStructure";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DataModelMappingsDataTable from "../../../components/DataTable/DataModelMappingsDataTable";
import AddCatalogModal from "../../../modals/AddCatalogModal";
import DataModelMappingModal from "../../../modals/DataModelMappingModal";

export default class DistributionContentsCsv extends Component {
    render() {
        const {contents, catalog, distribution, dataset} = this.props;

        return <div className="PageContainer">

            {distribution.includeAllData ? <div className="NoResults">
                This distribution contains all fields.
            </div> : <CSVStudyStructure
                studyId={distribution.study}
                distributionContents={contents}
                catalog={catalog}
                dataset={dataset}
                distribution={distribution.slug}
            />}
        </div>;
    }
}