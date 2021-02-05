import React, {Component} from 'react'
import './FieldListItem.scss'
import Checkbox from "../Input/Checkbox";
import {FieldIcons} from "../Icon/FieldIcons";
import {classNames} from "../../util";
import {Icon} from "@castoredc/matter";

export default class FieldListItem extends Component {
    render() {
        const { id, type, label, stepNumber, number, variableName, selectable, selected, onSelect, exportable, dataFormat, dataType}  = this.props;

        let isExportable = exportable.exportable;

        if(isExportable && dataFormat) {
            isExportable = exportable[dataFormat];
        }

        if(isExportable && dataType) {
            isExportable = exportable.dataTypes.includes(dataType);
        }

        return <div
            className={classNames('ListItem FieldListItem', ! isExportable && 'Disabled', selected && 'Selected')}
            onClick={isExportable ? () => onSelect(id, variableName, label) : undefined}
        >
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
        </div>;
    }
}