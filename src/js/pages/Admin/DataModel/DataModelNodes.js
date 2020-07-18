import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Button, DataTable, Stack, Tabs} from "@castoredc/matter";
import AddNodeModal from "../../../modals/AddNodeModal";

export default class DataModelNodes extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:      false,
            isLoadingNodes: true,
            hasLoadedNodes: false,
            nodes:          null,
            selectedType:   'internal',
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
                    nodes:          response.data,
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
            return <InlineLoader/>;
        }

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
                            title:   'Internal',
                            content: <DataTable
                                         emptyTableMessage="This data model does not have internal nodes"
                                         cellSpacing="default"
                                         rows={nodes.internal.map((item) => {
                                             return [
                                                 item.title,
                                                 item.value,
                                                 item.repeated ? {
                                                     type: 'tickSmall',
                                                 } : undefined,
                                             ];
                                         })}
                                         structure={{
                                             id:       {
                                                 header:    'Title',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             title:    {
                                                 header:    'Slug',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             repeated: {
                                                 header:    'Repeated',
                                                 resizable: true,
                                                 template:  'icon',
                                             },
                                         }}
                                     />,
                        },
                        external: {
                            title:   'External',
                            content: <DataTable
                                         emptyTableMessage="This data model does not have external nodes"
                                         cellSpacing="default"
                                         rows={nodes.external.map((item) => {
                                             return [item.title, item.value.prefixedValue, item.value.value];
                                         })}
                                         structure={{
                                             id:    {
                                                 header:    'Title',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             short: {
                                                 header:    'Short',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             title: {
                                                 header:    'URI',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                         }}
                                     />,
                        },
                        literal:  {
                            title:   'Literal',
                            content: <DataTable
                                         emptyTableMessage="This data model does not have literal nodes"
                                         cellSpacing="default"
                                         rows={nodes.literal.map((item) => {
                                             return [item.title, item.value, item.value.dataType];
                                         })}
                                         structure={{
                                             id:       {
                                                 header:    'Title',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             type:     {
                                                 header:    'Value',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             dataType: {
                                                 header:    'Data type',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                         }}
                                     />,
                        },
                        value:    {
                            title:   'Value',
                            content: <DataTable
                                         emptyTableMessage="This data model does not have value nodes"
                                         cellSpacing="default"
                                         rows={nodes.value.map((item) => {
                                             return [
                                                 item.title,
                                                 item.value.value,
                                                 item.value.dataType,
                                                 item.repeated ? {
                                                     type: 'tickSmall',
                                                 } : undefined,
                                             ];
                                         })}
                                         structure={{
                                             id:       {
                                                 header:    'Title',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             type:     {
                                                 header:    'Type of value',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             dataType: {
                                                 header:    'Data type',
                                                 resizable: true,
                                                 template:  'fixed',
                                             },
                                             repeated: {
                                                 header:    'Repeated',
                                                 resizable: true,
                                                 template:  'icon',
                                             },
                                         }}
                                     />,
                        },
                    }}
                />
            </div>
        </div>;
    }
}