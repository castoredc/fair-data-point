import React, {Component} from "react";
import {LinkContainer} from "react-router-bootstrap";
import StudiesDataTable from "components/DataTable/StudiesDataTable";
import {Button, Stack} from "@castoredc/matter";
import * as H from "history";

interface StudiesProps {
    catalog: string,
    history: H.History;
}

export default class Studies extends Component<StudiesProps> {
    render() {
        const {catalog, history} = this.props;

        return <div className="PageBody">
            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <LinkContainer to={'/admin/catalog/' + catalog + '/studies/add'}>
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