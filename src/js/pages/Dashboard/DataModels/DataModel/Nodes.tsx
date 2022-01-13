import React, {Component} from "react";
import {Button, CellText, DataGrid, Icon, IconCell, Stack, Tabs} from "@castoredc/matter";
import {RouteComponentProps} from "react-router-dom";
import AddNodeModal from "../../../../modals/AddNodeModal";

interface NodesProps extends RouteComponentProps<any> {
    nodes: any,
    getNodes: () => void,
    dataModel: any,
    version: any,
}

interface NodesState {
    showModal: boolean,
    selectedType: string,
}

export default class Nodes extends Component<NodesProps, NodesState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            selectedType: 'internal',
        };
    }

    openModal = () => {
        this.setState({showModal: true});
    };

    closeModal = () => {
        this.setState({showModal: false});
    };

    onSaved = () => {
        const {getNodes} = this.props;

        this.closeModal();

        getNodes();
    };

    changeTab = (tabIndex) => {
        this.setState({
            selectedType: tabIndex,
        });
    };

    render() {
        const {showModal, selectedType} = this.state;
        const {dataModel, nodes, version} = this.props;

        if (nodes === null) {
            return null;
        }

        const internalNodeRows = nodes.internal.map((item) => {
            return {
                title: <CellText>{item.title}</CellText>,
                value: <CellText>{item.value}</CellText>,
                repeated: item.repeated ? <IconCell icon={{type: 'tickSmall'}}/> : undefined,
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
                repeated: item.repeated ? <IconCell icon={{type: 'tickSmall'}}/> : undefined,
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