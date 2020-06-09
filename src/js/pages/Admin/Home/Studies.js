import React, {Component} from "react";
import axios from "axios/index";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, DataTable, Pagination} from "@castoredc/matter";
import {MethodType, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Filters from "../../../components/Filters";
import {classNames} from "../../../util";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";

export default class Studies extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
        };
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
        const {studies, isLoadingStudies, hasLoadedStudies, hasLoadedFilters, filterOptions, pagination, showModal} = this.state;
        const {history} = this.props;

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={12} className="PageTitle">
                    <div><h3>Studies</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={6}/>
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" onClick={this.openModal}>New study</Button>
                    </div>
                </Col>
            </Row>
            <StudiesDataTable
                history={history}
            />
        </div>;
    }
}