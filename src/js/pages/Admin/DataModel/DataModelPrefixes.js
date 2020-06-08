import React, {Component} from "react";
import axios from "axios/index";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Col, Row} from "react-bootstrap";
import {Button, DataTable} from "@castoredc/matter";
import DataModelPrefixModal from "../../../modals/DataModelPrefixModal";
import TripleModal from "../../../modals/TripleModal";
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
            prefixes:          null,
            prefixModalData:   null,
        };
    }

    componentDidMount() {
        this.getContents();
    }

    getContents = () => {
        const {dataModel} = this.props;

        this.setState({
            isLoadingContents: true,
        });

        axios.get('/api/model/' + dataModel.id + '/prefix')
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the distribution';
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
        const {dataModel} = this.props;
        const {prefixModalData} = this.state;

        axios.delete('/api/model/' + dataModel.id + '/prefix/' + prefixModalData.id)
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
        const {dataModel} = this.props;

        return <div>
            <DataModelPrefixModal
                show={showModal.add}
                handleClose={() => {
                    this.closeModal('add')
                }}
                onSaved={() => {
                    this.onSaved('add')
                }}
                modelId={dataModel.id}
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

            <Row>
                <Col sm={6}/>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" onClick={() => {
                            this.openModal('add', null)
                        }}>Add prefix</Button>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col sm={12}>
                    {isLoadingContents ? <InlineLoader/> : <DataTable
                        anchorRight={1}
                        emptyTableMessage="This data model does not have prefixes"
                        cellSpacing="default"
                        rows={prefixes.map((item) => {
                            return [
                                item.prefix,
                                item.uri,
                                [
                                    {
                                        destination: () => {
                                            this.openModal('add', {id: item.id, prefix: item.prefix, uri: item.uri})
                                        },
                                        label:       'Edit prefix',
                                    },
                                    {
                                        destination: () => {
                                            this.openModal('remove', {id: item.id, prefix: item.prefix, uri: item.uri})
                                        },
                                        label:       'Delete prefix',
                                    },
                                ],
                            ];
                        })}
                        structure={{
                            id:      {
                                header:    'Prefix',
                                resizable: true,
                                template:  'fixed',
                            },
                            title:   {
                                header:    'URI',
                                resizable: true,
                                template:  'fixed',
                            },
                            actions: {
                                header:   '',
                                template: 'rowAction',
                            },
                        }}
                    />}
                </Col>
            </Row>
        </div>;
    }
}