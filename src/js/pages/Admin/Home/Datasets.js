import React, {Component} from "react";
import {ViewHeader} from "@castoredc/matter";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";
import DocumentTitle from "../../../components/DocumentTitle";

export default class Datasets extends Component {
    render() {
        const {history} = this.props;

        return <div className="PageContainer">
            <div className="Page">
                <DocumentTitle title="FDP Admin | Datasets"/>
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