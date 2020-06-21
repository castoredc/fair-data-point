import React, {Component} from "react";
import StudiesDataTable from "../../../components/DataTable/StudiesDataTable";
import {Col, Row} from "react-bootstrap";
import {Button} from "@castoredc/matter";
import ButtonGroup from "react-bootstrap/ButtonGroup";
import AddStudyModal from "../../../modals/AddStudyModal";
import ConfirmModal from "../../../modals/ConfirmModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class CatalogAddStudy extends Component {
    constructor(props) {
        super(props);
        this.state = {
            displayFilter: false,
            showModal: {
                newStudy: false,
                confirm: false
            },
            selectedStudy: null,
            addedStudy: null
        };
    }

    toggleFilter = () => {
        this.setState({
            displayFilter: ! this.state.displayFilter
        });
    };

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
        const { displayFilter, showModal, selectedStudy, addedStudy } = this.state;

        return <div className="SubPage">
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

            <Row>
                <Col sm={6} />
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" className="AddButton" onClick={() => {this.openModal('newStudy')}}>Create new study</Button>

                        <ButtonGroup className="FilterButton">
                            <Button icon="filters" buttonType="secondary" onClick={this.toggleFilter} active={displayFilter}>
                                Filters
                            </Button>
                        </ButtonGroup>
                    </div>
                </Col>
            </Row>
            <StudiesDataTable
                onClick={this.handleStudyClick}
                displayOverlay={displayFilter}
                hideCatalog={catalog}
                lastHandledStudy={addedStudy}
                overlay
            />
        </div>;
    }
}