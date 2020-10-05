import React, {Component} from "react";
import {Button, Stack, ViewHeader} from "@castoredc/matter";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import AddStudyModal from "../../../modals/AddStudyModal";
import DocumentTitle from "../../../components/DocumentTitle";

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
            <DocumentTitle title="FDP Admin | Studies" />

            <AddStudyModal
                show={showModal}
                handleClose={this.closeModal}
            />

            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>Studies</ViewHeader>
                </div>

                <div className="PageBody">
                    <div className="PageButtons">
                        <Stack distribution="trailing" alignment="end">
                            <Button icon="add" onClick={this.openModal}>New study</Button>
                        </Stack>
                    </div>

                    <StudiesDataTable
                        history={history}
                    />
                </div>
            </div>
        </div>;
    }
}