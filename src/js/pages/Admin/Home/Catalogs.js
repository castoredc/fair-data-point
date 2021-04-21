import React, {Component} from "react";
import {Button, Stack, ViewHeader} from "@castoredc/matter";
import AddCatalogModal from "../../../modals/AddCatalogModal";
import DocumentTitle from "../../../components/DocumentTitle";
import CatalogsDataTable from "../../../components/DataTable/CatalogsDataTable";

export default class Catalogs extends Component {
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
        const {showModal} = this.state;

        return <div className="PageContainer">
            <DocumentTitle title="FDP Admin | Catalogs"/>
            <AddCatalogModal
                show={showModal}
                handleClose={this.closeModal}
            />
            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>Catalogs</ViewHeader>
                </div>

                <div className="PageBody">
                    <div className="PageButtons">
                        <Stack distribution="trailing" alignment="end">
                            <Button icon="add" onClick={this.openModal}>New catalog</Button>
                        </Stack>
                    </div>

                    <CatalogsDataTable
                        history={history}
                    />
                </div>
            </div>
        </div>;
    }
}