import React, {Component} from "react";
import {Col, Row} from "react-bootstrap";
import {Button} from "@castoredc/matter";
import {LinkContainer} from "react-router-bootstrap";
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
                        <LinkContainer to="/admin/studies/add">
                            <Button icon="add">New study</Button>
                        </LinkContainer>
                    </div>
                </Col>
            </Row>
            <StudiesDataTable
                history={history}
            />
        </div>;
    }
}