import React, {Component} from "react";
import axios from "axios/index";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, DataTable} from "@castoredc/matter";
import AddDataModelModal from "../../../modals/AddDataModelModal";

export default class DataModels extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataModels: true,
            hasLoadedDataModels: false,
            dataModels:          [],
            showModal:           false,
        };
    }

    componentDidMount() {
        this.getDataModels();
    }

    getDataModels = () => {
        this.setState({
            isLoadingDataModels: true,
        });

        axios.get('/api/model')
            .then((response) => {
                this.setState({
                    dataModels:          response.data,
                    isLoadingDataModels: false,
                    hasLoadedDataModels: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataModels: false,
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the data models';
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

    render() {
        const {dataModels, isLoadingDataModels, showModal} = this.state;
        const {history} = this.props;

        return <div className="PageContainer">
            <AddDataModelModal
                show={showModal}
                handleClose={this.closeModal}
            />

            <Row className="PageHeader">
                <Col sm={12} className="PageTitle">
                    <div><h3>Data models</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={6}/>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" onClick={this.openModal}>New data model</Button>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col sm={12} className="Page">
                    {isLoadingDataModels ? <InlineLoader/> : <div className="SelectableDataTable"><DataTable
                        emptyTableMessage="No data models found"
                        highlightRowOnHover
                        cellSpacing="default"
                        onClick={(event, rowID, index) => {history.push(`/admin/model/${dataModels[index].id}`)}}
                        rows={dataModels.map((item) => {
                            return [item.title];
                        })}
                        structure={{
                            title: {
                                header:    'Title',
                                resizable: true,
                                template:  'text',
                            },
                        }}
                    /></div>}
                </Col>
            </Row>
        </div>;
    }
}