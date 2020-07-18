import React, {Component} from "react";
import {LinkContainer} from "react-router-bootstrap";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import {Button, Stack} from "@castoredc/matter";

export default class CatalogStudies extends Component {
    render() {
        const {catalog, history} = this.props;

        return <div className="PageBody">
            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                        <LinkContainer to={'/admin/catalog/' + catalog.slug + '/studies/add'}>
                            <Button icon="add" className="AddButton">Add study</Button>
                        </LinkContainer>
                </Stack>
            </div>

            <StudiesDataTable
                history={history}
                catalog={catalog}
            />
        </div>;
    }
}