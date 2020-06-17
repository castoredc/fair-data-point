import React, {Component} from 'react'
import Modal from "../Modal";
import RDFStudyStructure from "../../components/StudyStructure/RDFStudyStructure";
import './DataModelMappingModal.scss';
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import Alert from "../../components/Alert";

export default class DataModelMappingModal extends Component {
    constructor(props) {
        super(props);
        this.state = {
            hasLoadedStructure:   false,
            structure:            null,
            isLoading:            true
        };
    }

    componentDidMount() {
        this.getStructure();
    }

    getStructure = () => {
        const { studyId } = this.props;

        this.setState({
            isLoading: true,
        });

        axios.get('/api/castor/study/' + studyId + '/structure/')
            .then((response) => {
                this.setState({
                    structure:          response.data,
                    isLoading:          false,
                    hasLoadedStructure: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    handleSelect = (event, data, selected) => {
        const { mapping, dataset, distribution, onSave } = this.props;

        this.setState({
            isLoading: true
        });

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf', {
            node: mapping.node.id,
            element: data.id
        })
            .then((response) => {
                this.setState({
                    isLoading: false
                }, () => {
                    onSave(response.data);
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the mappings';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const { show, handleClose, studyId, mapping } = this.props;
        const { structure, hasLoadedStructure, isLoading } = this.state;

        return <Modal
            show={show}
            handleClose={handleClose}
            className="DataModelMappingModal"
            title={mapping ? (mapping.element ? `Edit mapping for ${mapping.node.title}` : `Add mapping for ${mapping.node.title}`) : 'Add mapping'}
            closeButton
            isLoading={isLoading}
        >
            {mapping && <Alert
                variant="info"
                icon="info">
                {mapping.node.value.value === 'plain' && <span>Only fields that are supporting <strong>a plain {mapping.node.value.dataType} value</strong> can be selected.</span>}
                {mapping.node.value.value === 'annotated' && <span>Only fields that are supporting <strong>an annotated value</strong> can be selected.</span>}
            </Alert>}
            {hasLoadedStructure && <RDFStudyStructure
                studyId={studyId}
                mapping={mapping}
                structure={structure}
                onSelect={this.handleSelect}
            />}
        </Modal>
    }
}