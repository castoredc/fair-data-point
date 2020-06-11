import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import {classNames, localizedText} from "../../../util";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {DataTable} from "@castoredc/matter";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class Catalogs extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading:    true,
            hasError:     false,
            catalogs:     {}
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    }

    render() {
        const {catalogs, isLoading} = this.state;
        const {history} = this.props;

        if (isLoading) {
            return <InlineLoader />;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={12} className="PageTitle">
                    <div><h3>Catalogs</h3></div>
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