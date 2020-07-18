import React, {Component} from "react";
import {Button, Stack, ViewHeader} from "@castoredc/matter";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import AddStudyModal from "../../../modals/AddStudyModal";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";

export default class Datasets extends Component {
    render() {
        const { history } = this.props;

        return <div className="PageContainer">
            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>Datasets</ViewHeader>
                </div>

                <div className="PageBody">
                    <DatasetsDataTable
                        history={history}
                    />
                </div>
            </div>
        </div>;
    }
}