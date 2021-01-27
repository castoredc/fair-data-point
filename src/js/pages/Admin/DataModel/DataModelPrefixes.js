import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {ActionsCell, Button, CellText, DataGrid, Stack} from "@castoredc/matter";
import DataModelPrefixModal from "../../../modals/DataModelPrefixModal";
import ConfirmModal from "../../../modals/ConfirmModal";

export default class DataModelPrefixes extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:         {
                add:    false,
                remove: false,
            },
            isLoadingContents: true,
            hasLoadedContents: false,
            prefixes:          [],
            prefixModalData:   null,
        };
    }

    componentDidMount() {
        this.getContents();
    }

    getContents = () => {
        const {dataModel, version} = this.props;

        this.setState({
            isLoadingContents: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/prefix')
            .then((response) => {
                this.setState({
                    prefixes:          response.data,
                    isLoadingContents: false,
                    hasLoadedContents: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingContents: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    openModal = (type, data) => {
        const {showModal} = this.state;

        this.setState({
            showModal:       {
                ...showModal,
                [type]: true,
            },
            prefixModalData: data,
        });
    };

    closeModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    onSaved = (type) => {
        this.closeModal(type);
        this.getContents();
    };

    removePrefix = () => {
        const {dataModel, version} = this.props;
        const {prefixModalData} = this.state;

        axios.delete('/api/model/' + dataModel.id + '/v/' + version + '/prefix/' + prefixModalData.id)
            .then(() => {
                this.onSaved('remove');
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred"/>, {
                    position: "top-center",
                });
            });
    };

    render() {
        const {showModal, isLoadingContents, prefixes, prefixModalData} = this.state;
        const {dataModel, version} = this.props;

        const columns = [
            {
                Header: 'Prefix',
                accessor: 'prefix',
            },
            {
                Header: 'URI',
                accessor: 'uri',
            },
            {
                accessor: 'menu',
                disableGroupBy: true,
                disableResizing: true,
                isInteractive: true,
                isSticky: true,
                maxWidth: 34,
                minWidth: 34,
                width: 34
            }
        ]

        const rows = prefixes.map((item) => {
            const data = {id: item.id, prefix: item.prefix, uri: item.uri};

            return {
                prefix: <CellText>{item.prefix}</CellText>,
                uri: <CellText>{item.uri}</CellText>,
                menu: <ActionsCell items={[
                    {
                        destination: () => {
                            this.openModal('add', data)
                        },
                        label:       'Edit prefix',
                    },
                    {
                        destination: () => {
                            this.openModal('remove', data)
                        },
                        label:       'Delete prefix',
                    },
                ]} />,
            }
        });

        return <div className="PageBody">
            <DataModelPrefixModal
                show={showModal.add}
                handleClose={() => {
                    this.closeModal('add')
                }}
                onSaved={() => {
                    this.onSaved('add')
                }}
                modelId={dataModel.id}
                versionId={version}
                data={prefixModalData}
            />

            {prefixModalData && <ConfirmModal
                title="Delete prefix"
                action="Delete prefix"
                variant="danger"
                onConfirm={this.removePrefix}
                onCancel={() => {
                    this.closeModal('remove')
                }}
                show={showModal.remove}
            >
                Are you sure you want to delete prefix <strong>{prefixModalData.prefix}</strong>?
            </ConfirmModal>}

            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" onClick={() => {
                        this.openModal('add', null)
                    }}>Add prefix</Button>
                </Stack>
            </div>

            <div className="SelectableDataTable FullHeightDataTable" ref={this.tableRef}>
                {isLoadingContents ? <InlineLoader/> : <div className="DataTableWrapper">
                    <DataGrid
                        accessibleName="Prefixes"
                        anchorRightColumns={1}
                        emptyStateContent="This data model does not have prefixes"
                        rows={rows}
                        columns={columns}
                    />
                </div>}
            </div>
        </div>;
    }
}