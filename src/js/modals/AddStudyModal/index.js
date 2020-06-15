import React, {Component} from 'react'
import Modal from "../Modal";
import StudyForm from "../../components/Form/Admin/StudyForm";

export default class AddStudyModal extends Component {
    render() {
        const { show, handleClose } = this.props;

        return <Modal
            show={show}
            handleClose={handleClose}
            className="StudyModal"
            title="Add new study"
            closeButton
        >
            <StudyForm />
        </Modal>
    }
}