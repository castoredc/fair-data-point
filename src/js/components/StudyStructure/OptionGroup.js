import React, {Component} from 'react'
import {Button, Col, Row} from "react-bootstrap";
import Annotations from "../Annotations";
import Toggle from "../Toggle";
import {Icon} from "@castoredc/matter";

export default class OptionGroup extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            modalData: null,
        };
    }

    toggleModule = () => {
        const { expanded } = this.state;

        this.setState({
            expanded: !expanded
        });
    };

    openModal = (modalType) => {
        this.setState({
            modalType: modalType,
            showModal: true
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false
        });
    };

    render() {
        const { id, name, studyId, options, openModal } = this.props;
        const { expanded, showModal, modalType, newTriple } = this.state;

        return <Toggle title={name}>
            <div className="OptionGroupTable">
                <Row className="OptionGroupTableHeader">
                    <Col sm={3}>Option</Col>
                    <Col sm={2}>Value</Col>
                    <Col sm={6}>Annotations</Col>
                    <Col sm={1}/>
                </Row>
                <div className="OptionGroupTableBody">
                    {options.length === 0 ? <Col sm={12} className="NoResults">This option group does not contain options.</Col> : <div>
                        {options.map((option) => {
                            const data = {
                                type: "field_option",
                                id: option.id,
                                parent: id
                            };

                            return <Row className="OptionGroupItem" key={option.id}>
                                <Col sm={3}>{option.name}</Col>
                                <Col sm={2}>{option.value}</Col>
                                <Col sm={6}>
                                    <Annotations annotations={option.annotations} />
                                </Col>
                                <Col sm={1}>
                                    <Button variant="link" onClick={() => {openModal(data)}}>
                                        <Icon type="add" />
                                    </Button>
                                </Col>
                            </Row>;
                        })}
                    </div>
                    }
                </div>
            </div>
        </Toggle>;
    }
}