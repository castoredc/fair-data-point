import React, {Component} from "react";
import axios from "axios/index";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Col, Row} from "react-bootstrap";
import {Button, DataTable} from "@castoredc/matter";
import AddDataModelPrefixModal from "../../../modals/AddDataModelPrefixModal";

export default class DataModelPrefixes extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:             false,
            isLoadingContents:     true,
            hasLoadedContents:     false,
            prefixes:              null,
        };
    }

    componentDidMount() {
        this.getContents();
    }

    getContents = () => {
        const { dataModel } = this.props;

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

    onSaved = () => {
        this.setState({
            showModal: false
        });

        this.getContents();
    };

    render() {
        const {showModal, isLoadingContents, prefixes} = this.state;
        const {dataModel} = this.props;

        return <div>
            <AddDataModelPrefixModal
                show={showModal}
                handleClose={this.closeModal}
                onSaved={this.onSaved}
                modelId={dataModel.id}
            />
            <Row>
                <Col sm={6} />
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" onClick={this.openModal}>Add prefix</Button>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col sm={12}>
                    {isLoadingContents ? <InlineLoader/> : <DataTable
                            emptyTableMessage="This data model does not have prefixes"
                            cellSpacing="default"
                            rows={prefixes.map((item) => {
                                return [item.prefix, item.uri];
                            })}
                            structure={{
                                id:    {
                                    header:    'Prefix',
                                    resizable: true,
                                    template:  'fixed',
                                },
                                title: {
                                    header:    'URI',
                                    resizable: true,
                                    template:  'fixed',
                                },
                            }}
                        />}
                </Col>
            </Row>
        </div>;
    }
}