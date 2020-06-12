import React, {Component} from "react";
import {Col, Row} from "react-bootstrap";
import {Button} from "@castoredc/matter";
import {LinkContainer} from "react-router-bootstrap";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import AddStudyModal from "../../../modals/AddStudyModal";

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
        const { history } = this.props;
        const { showModal } = this.state;

        return <div className="PageContainer">
            <AddStudyModal
                show={showModal}
                handleClose={this.closeModal}
            />

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