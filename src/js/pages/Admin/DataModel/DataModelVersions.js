import React, {Component} from "react";
import {Button, DataTable, Stack} from "@castoredc/matter";
import DataModelVersionModal from "../../../modals/DataModelVersionModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class DataModelVersions extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:          false,
        };
    }

    openModal = () => {
        this.setState({
            showModal: true
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false
        });
    };

    createNewVersion = (version) => {
        const {dataModel, getDataModel} = this.props;

        axios.post('/api/model/' + dataModel.id + '/v', {
            type: version
        })
            .then((response) => {
                toast.success(<ToastContent type="success" message="A new version was successfully created" />, {
                    position: "top-right"
                });

                this.closeModal();

                getDataModel();
            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while creating a new version';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const {showModal} = this.state;
        const {dataModel} = this.props;

        const latestVersion = dataModel.versions.slice(-1)[0].version;

        return <div className="PageBody">
            <DataModelVersionModal
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
                emptyTableMessage="This data model does not have any versions"
                cellSpacing="default"
                rows={dataModel.versions.map((version) => {
                    return [version.version, version.count.modules, version.count.nodes];
                })}
                structure={{
                    version:    {
                        header:    'Version',
                        resizable: true,
                        template:  'fixed',
                    },
                    moduleCount:    {
                        header:    'Modules',
                        resizable: true,
                        template:  'fixed',
                    },
                    nodeCount:    {
                        header:    'Nodes',
                        resizable: true,
                        template:  'fixed',
                    },
                }}
            />
        </div>;
    }

}