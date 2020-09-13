import React, {Component} from 'react'
import Modal from "../Modal";
import CatalogForm from "../../components/Form/Admin/CatalogForm";

export default class AddCatalogModal extends Component {
    render() {
        const { show, handleClose } = this.props;

        return <Modal
            show={show}
            handleClose={handleClose}
            className="CatalogModal"
            title="Add new catalog"
            closeButton
        >
            <CatalogForm />
        </Modal>
    }
}