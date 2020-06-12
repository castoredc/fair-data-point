import React, {Component} from "react";

import {Col, Row} from "react-bootstrap";
import {LinkContainer} from "react-router-bootstrap";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import {Button} from "@castoredc/matter";

export default class CatalogStudies extends Component {
    constructor(props) {
        super(props);
        this.state = {
            displayFilter:     false,
        };
    }

    toggleFilter = () => {
        this.setState({
            displayFilter: ! this.state.displayFilter
        });
    };

    render() {
        const {pages, displayFilter} = this.state;
        const {catalog, history} = this.props;

        return <div className="SubPage">
            <Row>
                <Col sm={6} />
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <LinkContainer to={'/admin/catalog/' + catalog.slug + '/studies/add'}>
                            <Button icon="add" className="AddButton">Add study</Button>
                        </LinkContainer>

                        <ButtonGroup className="FilterButton">
                            <Button icon="filters" buttonType="secondary" onClick={this.toggleFilter} active={displayFilter}>
                                Filters
                            </Button>
                        </ButtonGroup>
                    </div>
                </Col>
            </Row>
            <StudiesDataTable
                history={history}
                catalog={catalog}
                displayOverlay={displayFilter}
                overlay
            />
        </div>;
    }
}