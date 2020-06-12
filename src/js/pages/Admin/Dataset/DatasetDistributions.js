import React, {Component} from "react";

import {Col, Row} from "react-bootstrap";
import {LinkContainer} from "react-router-bootstrap";
import {Button} from "@castoredc/matter";
import DistributionsDataTable from "../../../components/DataTable/DistributionsDataTable";

export default class DatasetDistributions extends Component {
    render() {
        const {catalog, dataset, history} = this.props;

        return <div className="SubPage">
            <Row>
                <Col sm={6} />
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <LinkContainer to={'/admin/' + (catalog ? '/catalog/' + catalog : '') + '/dataset/' + dataset.slug + '/distributions/add'}>
                            <Button icon="add" className="AddButton">Add distribution</Button>
                        </LinkContainer>
                    </div>
                </Col>
            </Row>
            <DistributionsDataTable
                history={history}
                catalog={catalog}
                dataset={dataset}
            />
        </div>;
    }
}