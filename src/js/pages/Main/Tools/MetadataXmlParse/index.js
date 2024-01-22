import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, CellText, DataGrid, FileSelector, Stack } from '@castoredc/matter';
import Layout from '../../../../components/Layout';
import MainBody from '../../../../components/Layout/MainBody';
import { downloadFile } from '../../../../util';
import './MetadataXmlParse.scss';
import DataGridContainer from '../../../../components/DataTable/DataGridContainer';
import { apiClient } from 'src/js/network';

export default class MetadataXmlParse extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: false,
            isLoaded: false,
            data: [],
        };
    }

    onFileChange = e => {
        if (e.target.files.length > 0) {
            const file = e.target.files[0];

            if (file.type !== 'text/xml') {
                toast.error(<ToastItem type="error" title="This file is not an XML file." />);

                return;
            }

            const formData = new FormData();

            formData.append('xml', file);

            this.setState({
                isLoading: true,
            });

            apiClient
                .post('/api/tools/metadata-xml-parse', formData, {
                    headers: {
                        'content-type': 'multipart/form-data',
                    },
                })
                .then(response => {
                    this.setState({
                        isLoading: false,
                        isLoaded: true,
                        data: response.data,
                    });
                })
                .catch(error => {
                    this.setState({
                        isLoading: false,
                    });

                    if (error.response && typeof error.response.data.error !== 'undefined') {
                        toast.error(<ToastItem type="error" title={error.response.data.error} />);
                    } else {
                        toast.error(<ToastItem type="error" title="An error occurred while transforming the XML." />);
                    }
                });
        }
    };

    generateCSV = () => {
        const { data } = this.state;

        const array = [Object.keys(data[0])].concat(data);

        const csvContent = array
            .map(it => {
                return Object.values(it).toString();
            })
            .join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        downloadFile(blob, 'metadata.csv');
    };

    render() {
        const { data, isLoading, isLoaded } = this.state;

        const title = 'Convert Metadata XML to CSV';

        const columns = [
            {
                Header: 'Variable',
                accessor: 'variable',
            },
            {
                Header: 'Metadata Type',
                accessor: 'type',
            },
            {
                Header: 'Metadata Value',
                accessor: 'value',
            },
            {
                Header: 'Metadata Description',
                accessor: 'description',
            },
        ];

        const rows = data.map(item => {
            return {
                variable: <CellText>{item.variableName}</CellText>,
                type: <CellText>{item.type}</CellText>,
                value: <CellText>{item.value}</CellText>,
                description: <CellText>{item.description}</CellText>,
            };
        });

        return (
            <Layout className="MetadataXmlParse" title={title}>
                <MainBody>
                    <div className="Top">
                        <h1>{title}</h1>

                        <Stack distribution="equalSpacing">
                            <FileSelector onChange={this.onFileChange} />

                            {isLoaded && (
                                <Button icon="download" onClick={this.generateCSV}>
                                    Download CSV
                                </Button>
                            )}
                        </Stack>
                    </div>

                    <DataGridContainer fullHeight isLoading={isLoading}>
                        <DataGrid accessibleName="Metadata" emptyStateContent="No metadata found" rows={rows} columns={columns} />
                    </DataGridContainer>
                </MainBody>
            </Layout>
        );
    }
}
