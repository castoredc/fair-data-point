import React, {Component} from "react";
import axios from "axios";

import {Col, Row} from "react-bootstrap";
import {classNames, localizedText} from "../../../util";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {Button, DataTable} from "@castoredc/matter";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import AddStudyModal from "../../../modals/AddStudyModal";
import AddCatalogModal from "../../../modals/AddCatalogModal";

export default class Catalogs extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading:    true,
            hasError:     false,
            catalogs:     {},
            showModal:    false,
        };
    }

    componentDidMount() {
        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs:   response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message}/>);
            });
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

    render() {
        const {catalogs, isLoading, showModal} = this.state;
        const {history} = this.props;

        if (isLoading) {
            return <InlineLoader />;
        }

        return <div className="PageContainer">
            <AddCatalogModal
                show={showModal}
                handleClose={this.closeModal}
            />

            <Row className="PageHeader">
                <Col sm={12} className="PageTitle">
                    <div><h3>Catalogs</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={6}/>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" onClick={this.openModal}>New catalog</Button>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col sm={12} className="Page">
                    <div className={classNames('SelectableDataTable FullHeightDataTable', isLoading && 'Loading')}>
                        <div className="DataTableWrapper">
                            <DataTable
                                emptyTableMessage="No catalogs found"
                                highlightRowOnHover
                                cellSpacing="default"
                                onClick={(event, rowID, index) => {
                                    if(typeof index !== "undefined") {
                                        history.push(`/admin/catalog/${catalogs[index].slug}`)
                                    }
                                }}
                                rows={catalogs.map((item) => {
                                    return [
                                        item.hasMetadata ? localizedText(item.metadata.title, 'en') : '',
                                        item.hasMetadata ? localizedText(item.metadata.description, 'en') : ''
                                    ];
                                })}
                                structure={{
                                    title: {
                                        header:    'Name',
                                        resizable: true,
                                        template:  'text',
                                    },
                                    type: {
                                        header:    'Description',
                                        resizable: true,
                                        template:  'text',
                                    },
                                }}
                            />
                        </div>
                    </div>
                </Col>
            </Row>
        </div>;
    }
}