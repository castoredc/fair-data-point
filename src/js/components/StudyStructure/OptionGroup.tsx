import React, {Component} from 'react'
import Annotations from "../Annotations";
import {Button} from "@castoredc/matter";
import AddAnnotationModal from "../../modals/AddAnnotationModal";
import './StudyStructure.scss';

interface OptionGroupProps {
    studyId: string,
    id: string,
    options: any,
    onUpdate: () => void,
}

interface OptionGroupState {
    showModal: boolean,
    modalData: any,
}

export default class OptionGroup extends Component<OptionGroupProps, OptionGroupState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            modalData: null,
        };
    }

    openModal = (modalData) => {
        this.setState({
            modalData: modalData,
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
            modalData: null,
        });
    };

    render() {
        const { studyId, id, options, onUpdate} = this.props;
        const { showModal, modalData } = this.state;

        return <div className="OptionGroupTable LargeTable">
            <AddAnnotationModal
                open={showModal}
                entity={modalData}
                onClose={this.closeModal}
                studyId={studyId}
                onSaved={onUpdate}
            />

            <div className="OptionGroupTableHeader TableHeader">
                <div className="OptionGroupTableOption">Option</div>
                <div className="OptionGroupTableValue">Value</div>
                <div className="OptionGroupTableAnnotations">
                    <div className="Annotation">
                        <div className="OntologyName">Ontology</div>
                        <div className="ConceptDisplayName">Display name</div>
                        <div className="ConceptCode">Concept ID</div>
                    </div>
                </div>
                <div className="OptionGroupTableButton"/>
            </div>
            <div className="OptionGroupTableBody TableBody">
                {options.length === 0 ?
                    <div className="NoResults">This option group does not contain options.</div> : <div>
                        {options.map((option) => {
                            const data = {
                                type:   "field_option",
                                id:     option.id,
                                title:  option.name,
                                parent: id,
                            };

                            return <div className="OptionGroupItem" key={option.id}>
                                <div className="OptionGroupTableOption">{option.name}</div>
                                <div className="OptionGroupTableValue">{option.value}</div>
                                <div className="OptionGroupTableAnnotations">
                                    <Annotations annotations={option.annotations}/>
                                </div>
                                <div className="OptionGroupTableButton">
                                    <Button onClick={() => {
                                        this.openModal(data)
                                    }} icon="add" iconDescription="Add annotation" buttonType="secondary" />
                                </div>
                            </div>;
                        })}
                    </div>
                }
            </div>
        </div>;
    }
}