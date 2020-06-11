import React, {Component} from "react";

import {Col, Row} from "react-bootstrap";
import {LinkContainer} from "react-router-bootstrap";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import {Button} from "@castoredc/matter";
import DatasetsDataTable from "../../../components/DataTable/DatasetsDataTable";

export default class CatalogDatasets extends Component {
    render() {
        const {catalog, history} = this.props;

        return <div className="SubPage">
            <Row>
                <Col sm={6} />
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <LinkContainer to={'/admin/catalog/' + catalog.slug + '/datasets/add'}>
                            <Button icon="add" className="AddButton">Add dataset</Button>
                        </LinkContainer>
                    </div>
                </Col>
            </Row>
            <DatasetsDataTable
                history={history}
                catalog={catalog}
            />
        </div>;
    }
}