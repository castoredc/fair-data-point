import React, {Component} from "react";
import {Button, DataTable, Stack} from "@castoredc/matter";
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

        const rows = new Map(dataDictionary.versions.map((version) => {
            return [
                version.id,
                {
                    cells: [
                        version.version,
                        version.count.modules,
                        version.count.nodes,
                    ],
                },
            ];
        }));

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

            <DataTable
                emptyTableMessage="This data dictionary does not have any versions"
                cellSpacing="default"
                rows={rows}
                structure={{
                    version:     {
                        header:    'Version',
                        resizable: true,
                        template:  'fixed',
                    },
                    moduleCount: {
                        header:    'Groups',
                        resizable: true,
                        template:  'fixed',
                    },
                    nodeCount:   {
                        header:    'Nodes',
                        resizable: true,
                        template:  'fixed',
                    },
                }}
            />
        </div>;
    }

}