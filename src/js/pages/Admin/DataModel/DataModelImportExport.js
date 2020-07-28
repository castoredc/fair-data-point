import React, {Component} from "react";
import {Button, Heading} from "@castoredc/matter";
import axios from "axios";
import {downloadFile} from "../../../util";

export default class DataModelImportExport extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isExporting: false,
        };
    }

    export = () => {
        const {dataModel, version} = this.props;

        this.setState({
            isExporting: true,
        });

        axios({
            url: '/api/model/' + dataModel.id + '/v/' + version + '/export',
            method: 'GET',
            responseType: 'blob',
        }).then((response) => {
            console.log(response);

            const contentDisposition = response.headers["content-disposition"];
            const match = contentDisposition.match(/filename\s*=\s*"(.+)"/i);
            const filename = match[1];

            this.setState({
                isExporting: false,
            });

            downloadFile(response.data, filename);
        });
    };


    render() {
        const { dataModel } = this.props;
        const { isExporting } = this.state;

        return <div className="PageBody">
            <div>
                <Heading type="Subsection">Export model</Heading>

                <Button onClick={this.export} icon="download" disabled={isExporting}>Export model</Button>
            </div>
        </div>;
    }

}