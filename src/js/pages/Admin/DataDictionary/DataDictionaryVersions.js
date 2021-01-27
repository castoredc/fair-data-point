import React, {Component} from "react";
import {Button, CellText, DataGrid, Stack} from "@castoredc/matter";
import DataDictionaryVersionModal from "../../../modals/DataDictionaryVersionModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class DataDictionaryVersions extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
        };
    }

    openModal = () => {
        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    createNewVersion = (version) => {
        const {dataDictionary, getDataDictionary} = this.props;

        axios.post('/api/dictionary/' + dataDictionary.id + '/v', {
            type: version,
        })
            .then((response) => {
                toast.success(<ToastContent type="success" message="A new version was successfully created"/>, {
                    position: "top-right",
                });

                this.closeModal();

                getDataDictionary();
            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while creating a new version';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {showModal} = this.state;
        const {dataDictionary} = this.props;

        const latestVersion = dataDictionary.versions.slice(-1)[0].version;

        const columns = [
            {
                Header:    'Version',
                accessor: 'version',
            },
            {
                Header:    'Groups',
                accessor: 'moduleCount',
            },
            {
                Header:    'Nodes',
                accessor: 'nodeCount',
            },
        ];

        const rows = dataDictionary.versions.map((version) => {
            return {
                version: <CellText>{version.version}</CellText>,
                moduleCount: <CellText>{version.count.modules}</CellText>,
                nodeCount: <CellText>{version.count.nodes}</CellText>,
            }
        });

        return <div className="PageBody">
            <DataDictionaryVersionModal
                show={showModal}
                latestVersion={latestVersion}
                handleClose={() => {
                    this.closeModal()
                }}
                onSave={this.createNewVersion}
            />

            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" onClick={this.openModal}>Create version</Button>
                </Stack>
            </div>

            <DataGrid
                accessibleName="Data model versions"
                emptyStateContent="This data dictionary does not have any versions"
                rows={rows}
                columns={columns}
            />
        </div>;
    }

}