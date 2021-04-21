import React, {Component} from 'react'
import './FieldListItem.scss'
import {FieldIcons} from "../Icon/FieldIcons";
import {classNames} from "../../util";
import {Icon} from "@castoredc/matter";

export default class FieldListItem extends Component {
    render() {
        const { id, type, label, stepNumber, number, variableName, selected, onSelect, exportable, dataFormat, dataType, dataTransformation}  = this.props;

        let isExportable = exportable.exportable;

        if(isExportable && dataTransformation) {
            isExportable = true;
        } else if(isExportable && dataType) {
            isExportable = exportable.dataTypes.includes(dataType);
        } else if(isExportable && dataFormat) {
            isExportable = exportable[dataFormat];
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