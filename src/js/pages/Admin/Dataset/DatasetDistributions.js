import React, {Component} from "react";
import {LinkContainer} from "react-router-bootstrap";
import {Button, Stack} from "@castoredc/matter";
import DistributionsDataTable from "../../../components/DataTable/DistributionsDataTable";

export default class DatasetDistributions extends Component {
    render() {
        const {catalog, dataset, history} = this.props;

        return <div className="PageBody">
            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <LinkContainer to={'/admin/dataset/' + dataset.slug + '/distributions/add'}>
                        <Button icon="add" className="AddButton">Add distribution</Button>
                    </LinkContainer>
                </Stack>
            </div>

            <DistributionsDataTable
                history={history}
                catalog={catalog}
                dataset={dataset}
            />
        </div>;
    }
}