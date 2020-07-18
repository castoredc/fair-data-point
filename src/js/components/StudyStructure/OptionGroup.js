import React, {Component} from 'react'
import {Button} from "react-bootstrap";
import Annotations from "../Annotations";
import {Icon} from "@castoredc/matter";

export default class OptionGroup extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            modalData: null,
        };
    }

    openModal = (modalType) => {
        this.setState({
            modalType: modalType,
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    render() {
        const {id, options, openModal} = this.props;

        return <div className="OptionGroupTable LargeTable">
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
                                parent: id,
                            };

                            return <div className="OptionGroupItem" key={option.id}>
                                <div className="OptionGroupTableOption">{option.name}</div>
                                <div className="OptionGroupTableValue">{option.value}</div>
                                <div className="OptionGroupTableAnnotations">
                                    <Annotations annotations={option.annotations}/>
                                </div>
                                <div className="OptionGroupTableButton">
                                    <Button variant="link" onClick={() => {
                                        openModal(data)
                                    }}>
                                        <Icon type="add"/>
                                    </Button>
                                </div>
                            </div>;
                        })}
                    </div>
                }
            </div>
        </div>;
    }
}