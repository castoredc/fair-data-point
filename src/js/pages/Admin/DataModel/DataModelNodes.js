import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Button, CellText, DataGrid, Icon, IconCell, LoadingOverlay, Stack, Tabs} from "@castoredc/matter";
import AddNodeModal from "../../../modals/AddNodeModal";

export default class DataModelNodes extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            isLoadingNodes: true,
            hasLoadedNodes: false,
            nodes: null,
            selectedType: 'internal',
        };
    }

    componentDidMount() {
        this.getNodes();
    }

    getNodes = () => {
        const {dataModel, version} = this.props;

        this.setState({
            isLoadingNodes: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/node')
            .then((response) => {
                this.setState({
                    nodes: response.data,
                    isLoadingNodes: false,
                    hasLoadedNodes: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingNodes: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the nodes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

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

    onSaved = () => {
        this.setState({
            showModal: false,
        });

        this.getNodes();
    };

    changeTab = (tabIndex) => {
        this.setState({
            selectedType: tabIndex,
        });
    };

    render() {
        const {showModal, isLoadingNodes, nodes, selectedType} = this.state;
        const {dataModel, version} = this.props;

        if (isLoadingNodes) {
            return <LoadingOverlay accessibleLabel="Loading nodes"/>;
        }

        const internalNodeRows = nodes.internal.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value}</CellText>,
                repeated: item.repeated ? <IconCell icon={{ type: 'tickSmall' }} /> : undefined,
            };
        });

        const externalNodeRows = nodes.external.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                short: <CellText>{item.value.prefixedValue}</CellText>,
                uri: <CellText>{item.value.value}</CellText>,
            };
        });

        const literalNodeRows = nodes.literal.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value.value}</CellText>,
                dataType: <CellText>{item.value.dataType}</CellText>,
            };
        });

        const valueNodeRows = nodes.value.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                type: <CellText>{item.value.value}</CellText>,
                dataType: <CellText>{item.value.dataType}</CellText>,
                repeated: item.repeated ? <IconCell icon={{ type: 'tickSmall' }} /> : undefined,
            };
        });

        return <div className="PageBody">
            <AddNodeModal
                show={showModal}
                handleClose={this.closeModal}
                onSaved={this.onSaved}
                type={selectedType}
                modelId={dataModel.id}
                versionId={version}
            />

            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" onClick={this.openModal}>Add {selectedType} node</Button>
                </Stack>
            </div>

            <div className="PageTabs">
                <Tabs
                    onChange={this.changeTab}
                    selected={selectedType}
                    tabs={{
                        internal: {
                            title: 'Internal',
                            content: <DataGrid
                                accessibleName="Internal nodes"
                                emptyStateContent="This data model does not have internal nodes"
                                rows={internalNodeRows}
                                columns={[
                                    {
                                        Header: 'Title',
                                        accessor: 'title',
                                    },
                                    {
                                        Header: 'Slug',
                                        accessor: 'value',
                                    },
                                    {
                                        Header: <Icon description="Repeated" type="tickSmall"/>,
                                        accessor: 'repeated',
                                        disableResizing: true,
                                        isInteractive: true,
                                        width: 32
                                    },
                                ]}
                            />,
                        },
                        external: {
                            title: 'External',
                            content: <DataGrid
                                accessibleName="External nodes"
                                emptyStateContent="This data model does not have external nodes"
                                rows={externalNodeRows}
                                columns={[
                                    {
                                        Header: 'Title',
                                        accessor: 'title',
                                    },
                                    {
                                        Header: 'Short',
                                        accessor: 'short',
                                    },
                                    {
                                        Header: 'URI',
                                        accessor: 'uri',
                                    },
                                ]}
                            />,
                        },
                        literal: {
                            title: 'Literal',
                            content: <DataGrid
                                accessibleName="Literal nodes"
                                emptyStateContent="This data model does not have literal nodes"
                                rows={literalNodeRows}
                                columns={[
                                    {
                                        Header: 'Title',
                                        accessor: 'title',
                                    },
                                    {
                                        Header: 'Value',
                                        accessor: 'value',
                                    },
                                    {
                                        Header: 'Data type',
                                        accessor: 'dataType',
                                    },
                                ]}
                            />,
                        },
                        value: {
                            title: 'Value',
                            content: <DataGrid
                                accessibleName="Value nodes"
                                emptyStateContent="This data model does not have value nodes"
                                rows={valueNodeRows}
                                columns={[
                                    {
                                        Header: 'Title',
                                        accessor: 'title',
                                    },
                                    {
                                        Header: 'Type of value',
                                        accessor: 'type',
                                    },
                                    {
                                        Header: 'Data type',
                                        accessor: 'dataType',
                                    },
                                    {
                                        Header: <Icon description="Repeated" type="tickSmall"/>,
                                        accessor: 'repeated',
                                        disableResizing: true,
                                        isInteractive: true,
                                        width: 32
                                    },
                                ]}
                            />,
                        },
                    }}
                />
            </div>
        </div>;
    }
}