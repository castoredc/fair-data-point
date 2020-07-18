import React, {Component} from "react";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import {Button, Stack} from "@castoredc/matter";
import AddStudyModal from "../../../modals/AddStudyModal";
import ConfirmModal from "../../../modals/ConfirmModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class CatalogAddStudy extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                newStudy: false,
                confirm: false
            },
            selectedStudy: null,
            addedStudy: null
        };
    }

    openModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal:       {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModal = (type) => {
        const {showModal} = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    handleStudyClick = (study) => {
        this.setState({
            selectedStudy: study,
        }, () => {
            this.openModal('confirm');
        })
    };

    handleAdd = () => {
        const { catalog } = this.props;
        const { selectedStudy } = this.state;

        axios.post('/api/catalog/' + catalog.slug + '/study/add', {
            studyId: selectedStudy.id
        })
            .then((response) => {
                toast.success(<ToastContent type="success" message="The study was successfully added to the catalog" />, {
                    position: "top-right"
                });

                this.closeModal('confirm');

                this.setState({
                    addedStudy: selectedStudy,
                });

            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while adding the study to the catalog';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { catalog } = this.props;
        const { showModal, selectedStudy, addedStudy } = this.state;

        return <div className="PageBody">
            <AddStudyModal
                show={showModal.newStudy}
                handleClose={() => {this.closeModal('newStudy')}}
                catalog={catalog}
            />

            {selectedStudy && <ConfirmModal
                title="Add study"
                action="Add study"
                variant="primary"
                onConfirm={this.handleAdd}
                onCancel={() => {this.closeModal('confirm')}}
                show={showModal.confirm}
            >
                Are you sure you want to add <strong>{selectedStudy.name}</strong> to this catalog?
            </ConfirmModal>}



            <div className="PageButtons">
                <Stack distribution="trailing" alignment="end">
                    <Button icon="add" className="AddButton" onClick={() => {this.openModal('newStudy')}}>Create new study</Button>
                </Stack>
            </div>

            <StudiesDataTable
                onClick={this.handleStudyClick}
                hideCatalog={catalog}
                lastHandledStudy={addedStudy}
            />
        </div>;
    }
}