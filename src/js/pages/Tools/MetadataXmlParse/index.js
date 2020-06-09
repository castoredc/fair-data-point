import React, {Component} from "react";
import {Col, Row} from "react-bootstrap";
import Form from "react-bootstrap/Form";
import bsCustomFileInput from "bs-custom-file-input";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import BootstrapTable from 'react-bootstrap-table-next';
import ToolkitProvider from 'react-bootstrap-table2-toolkit';
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import Icon from "../../../components/Icon";

export default class MetadataXmlParse extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: false,
            data: []
        };
    }

    componentDidMount() {
        bsCustomFileInput.init()
    }

    onFileChange = (e) => {
        if(e.target.files.length > 0) {
            const file = e.target.files[0];
            const formData = new FormData();

            formData.append('xml', file);

            this.setState({
                isLoading: true
            });

            axios.post('/api/tools/metadata-xml-parse', formData, {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            })
                .then((response) => {
                    this.setState({
                        isLoading: false,
                        data:      response.data
                    });
                })
                .catch((error) => {
                    this.setState({
                        isLoading: false
                    });

                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error"
                                                  message="An error occurred while transforming the XML."/>);
                    }
                });
        }
    };

    render() {
        const columns = [{
            dataField: 'id',
            text: 'Metadata ID',
            hidden: true
        }, {
            dataField: 'variableName',
            text: 'Variable'
        }, {
            dataField: 'type',
            text: 'Metadata Type'
        }, {
            dataField: 'value',
            text: 'Metadata Value'
        }, {
            dataField: 'description',
            text: 'Metadata Description'
        }];

        const defaultSorted = [{
            dataField: 'variableName',
            order: 'desc'
        }];

        return <FAIRDataInformation
            className="FAIRDataPoint"
            title={"Convert Metadata XML to CSV"}
        >
            <Row className="justify-content-md-center">
                <Col className="InformationCol" md={6}>
                    <Form>
                        <Form.File
                            id="custom-file"
                            label="Upload Castor Form XML"
                            custom
                            onChange={ this.onFileChange }
                            accept=".xml"
                        />
                    </Form>
                </Col>
            </Row>
            {this.state.isLoading && <Row><Col md={12}><InlineLoader/></Col></Row>}
                {this.state.data.length > 0 && <ToolkitProvider
                    keyField="id"
                    data={ this.state.data }
                    columns={ columns }
                    defaultSorted={ defaultSorted }
                    exportCSV
                    bootstrap4
                >
                    {
                        props => (<div className="Children Results">
                            <Row className="ResultsHeader">
                                <Col md={8}><h2>Metadata</h2></Col>
                                <Col md={4} className="ResultsHeaderButtons">
                                    <button className="btn btn-primary" onClick={ () => {props.csvProps.onExport()} }>
                                        <Icon type="download" /> Export to CSV
                                    </button>
                                </Col>
                            </Row>
                            <Row>
                                <Col>
                                    <BootstrapTable { ...props.baseProps } />
                                </Col>
                            </Row>
                        </div>
                        )
                    }
                </ToolkitProvider>
            }
        </FAIRDataInformation>;
    }
}
