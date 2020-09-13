import React, {Component} from 'react'
import Modal from "../Modal";
import RDFStudyStructure from "../../components/StudyStructure/RDFStudyStructure";
import './DataModelMappingModal.scss';
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import Alert from "../../components/Alert";
import StructureTypes from "../../components/StudyStructure/StructureTypes";

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

    handleSelect = (data) => {
        const { mapping, dataset, distribution, onSave, versionId, type } = this.props;

        this.setState({
            isLoading: true
        });

        const newData = {
            ...data,
            type: type,
        };

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/contents/rdf/v/' + versionId + '/' + type, newData)
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

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while saving the mapping';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {show, handleClose, studyId, mapping, type} = this.props;
        const {structure, hasLoadedStructure, isLoading} = this.state;

        if (type === 'node') {
            let valueDescription = '';
            let fieldDescription = <span>fields</span>;

            if (mapping) {
                if (mapping.node.value.value === 'plain') {
                    valueDescription = 'a plain ' + mapping.node.value.dataType + ' value';
                } else if (mapping.node.value.value === 'annotated') {
                    valueDescription = 'an annotated value';
                }

                if (mapping.node.repeated) {
                    fieldDescription = <span><b>repeated</b> fields</span>;
                }
            }

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
                    <span>Only {fieldDescription} that are supporting <b>{valueDescription}</b> can be selected.</span>
                </Alert>}
                {hasLoadedStructure && <RDFStudyStructure
                    studyId={studyId}
                    mapping={mapping}
                    structure={structure}
                    onSelect={this.handleSelect}
                />}
            </Modal>
        }

        else if (type === 'module') {
            return <Modal
                show={show}
                handleClose={handleClose}
                className="DataModelModuleMappingModal"
                title={mapping ? (mapping.element ? `Edit mapping for ${mapping.module.displayName}` : `Add mapping for ${mapping.module.displayName}`) : 'Add mapping'}
                closeButton
                isLoading={isLoading}
            >
                {hasLoadedStructure && <StructureTypes
                    mapping={mapping}
                    structure={structure}
                    onSelect={this.handleSelect}
                />}
            </Modal>
        }

        else {
            return null;
        }
    }
}