import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../../components/ToastContent";
import Layout from "../../../../components/Layout";
import MainBody from "../../../../components/Layout/MainBody";
import {Button, DataTable, FileSelector, Stack} from "@castoredc/matter";
import {classNames, downloadFile} from "../../../../util";
import InlineLoader from "../../../../components/LoadingScreen/InlineLoader";
import './MetadataXmlParse.scss';

export default class MetadataXmlParse extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: false,
            isLoaded: false,
            data: []
        };
    }

    onFileChange = (e) => {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];

            if(file.type !== 'text/xml') {
                toast.error(<ToastContent type="error"
                                          message="This file is not an XML file."/>);

                return;
            }

            const formData = new FormData();

            formData.append('xml', file);

            this.setState({
                isLoading: true
            });

            axios.post('/api/tools/metadata-xml-parse', formData, {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            })
                .then((response) => {
                    this.setState({
                        isLoading: false,
                        isLoaded: true,
                        data: response.data
                    });
                })
                .catch((error) => {
                    this.setState({
                        isLoading: false
                    });

                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error"
                                                  message="An error occurred while transforming the XML."/>);
                    }
                });
        }
    };

    generateCSV = () => {
        const {data} = this.state;

        const array = [Object.keys(data[0])].concat(data);

        const csvContent = array.map(it => {
            return Object.values(it).toString()
        }).join('\n');

        const blob = new Blob([csvContent], {type: "text/csv"});
        downloadFile(blob, 'metadata.csv');
    };

    render() {
        const {data, isLoading, isLoaded} = this.state;

        const title = 'Convert Metadata XML to CSV';

        const rows = new Map(data.map((item) => {
            return [
                item.id,
                {
                    cells: [
                        item.variableName,
                        item.type,
                        item.value,
                        item.description
                    ],
                },
            ];
        }));

        return <Layout
            className="MetadataXmlParse"
            title={title}
        >
            <MainBody>
                <div className="Top">
                    <h1>
                        {title}
                    </h1>

                    <Stack distribution="equalSpacing">
                        <FileSelector
                            onChange={this.onFileChange}
                        />

                        {isLoaded && <Button icon="download" onClick={this.generateCSV}>Download CSV</Button>}
                    </Stack>
                </div>

                <div className={classNames('DataTable FullHeightDataTable', isLoading && 'Loading', !isLoaded && 'NotLoaded')}>
                    {isLoading && <InlineLoader overlay={true} />}
                    <div className="DataTableWrapper">
                        <DataTable
                            emptyTableMessage="No metadata found"
                            highlightRowOnHover
                            cellSpacing="default"
                            onClick={this.onRowClick}
                            rows={rows}
                            structure={{
                                variable: {
                                    header: 'Variable',
                                    resizable: true,
                                    template: 'text',
                                },
                                type: {
                                    header: 'Metadata Type',
                                    resizable: true,
                                    template: 'text',
                                },
                                value: {
                                    header: 'Metadata Value',
                                    resizable: true,
                                    template: 'text',
                                },
                                description: {
                                    header: 'Metadata Description',
                                    resizable: true,
                                    template: 'text',
                                },
                            }}
                        />
                    </div>
                </div>
            </MainBody>
        </Layout>;
    }
}
