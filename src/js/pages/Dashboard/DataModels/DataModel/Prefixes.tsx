import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import {ActionsCell, Button, CellText, DataGrid, Stack} from "@castoredc/matter";
import {RouteComponentProps} from "react-router-dom";
import ToastContent from "components/ToastContent";
import DataModelPrefixModal from "../../../../modals/DataModelPrefixModal";
import ConfirmModal from "../../../../modals/ConfirmModal";
import DataGridContainer from "components/DataTable/DataGridContainer";

interface PrefixesProps extends RouteComponentProps<any> {
    prefixes: any,
    getPrefixes: () => void,
    dataModel: any,
    version: any,
}

interface PrefixesState {
    showModal: any,
    prefixModalData: any,
}

export default class Prefixes extends Component<PrefixesProps, PrefixesState> {
    private tableRef: React.RefObject<unknown>;

    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            prefixModalData: null,
        };

        this.tableRef = React.createRef();
    }

    openModal = (type, data) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
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
        const {getPrefixes} = this.props;
        this.closeModal(type);
        getPrefixes();
    };

    removePrefix = () => {
        const {dataModel, version} = this.props;
        const {prefixModalData} = this.state;

        axios.delete('/api/model/' + dataModel.id + '/v/' + version + '/prefix/' + prefixModalData.id)
            .then(() => {
                toast.success(<ToastContent type="success" message={<>
                    The <strong>{prefixModalData.title}</strong> prefix was successfully removed
                </>}
                />, {
                    position: "top-right",
                });

                this.onSaved('remove');
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred"/>, {
                    position: "top-center",
                });
            });
    };

    render() {
        const {showModal, prefixModalData} = this.state;
        const {dataModel, prefixes, version} = this.props;

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
                        label: 'Edit prefix',
                    },
                    {
                        destination: () => {
                            this.openModal('remove', data)
                        },
                        label: 'Delete prefix',
                    },
                ]}/>,
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

            <DataGridContainer
                fullHeight
                ref={this.tableRef}
            >
                <DataGrid
                    accessibleName="Prefixes"
                    anchorRightColumns={1}
                    emptyStateContent="This data model does not have prefixes"
                    rows={rows}
                    columns={columns}
                />
            </DataGridContainer>
        </div>;
    }
}