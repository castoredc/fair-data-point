import React, {Component} from 'react'
import './StudyListItem.scss'
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Icon from '../Icon';
import Checkbox from "../Input/Checkbox";
import {FieldIcons} from "../Icon/FieldIcons";

export default class FieldListItem extends Component {
    render() {
        const { id, type, label, stepNumber, number, variableName, selectable, selected, onSelect, children}  = this.props;

        const data = {
            id,
            type,
            label,
            stepNumber,
            number,
            variableName
        };

        const changeFunction = typeof onSelect !== 'undefined' ? (event) => { onSelect(event, data, !selected) } : null;

        return <Row className="ListItem FieldListItem" onClick={changeFunction}>
            <Col md={9} className="FieldListItemType">
                {selectable && <div className="FieldCheckbox">
                    <Checkbox
                        value={selected}
                        required={false}
                        name={id}
                        onChange={() => {}}
                    />
                </div>}
                <div className="FieldNumber">
                    {stepNumber}.{number}
                </div>
                <div className="FieldIcon">
                    <Icon type={FieldIcons[type]} height={12} width={12} />
                </div>
                <div className="FieldLabel">
                    {label}
                    <span className="FieldVariableName">{variableName}</span>
                </div>
            </Col>
            <Col md={3} className="FieldListItemActions">
                {children}
            </Col>
        </Row>;
    }
}