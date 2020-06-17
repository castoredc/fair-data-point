import React, {Component} from 'react'
import './FieldListItem.scss'
import Icon from '../Icon';
import Checkbox from "../Input/Checkbox";
import {FieldIcons} from "../Icon/FieldIcons";
import {classNames} from "../../util";

export default class FieldListItem extends Component {
    render() {
        const { id, type, label, stepNumber, number, variableName, selectable, selected, onSelect, exportable, dataFormat, dataType}  = this.props;

        const data = {
            id,
            type,
            label,
            stepNumber,
            number,
            variableName
        };

        let isExportable = exportable.exportable;

        if(isExportable && dataFormat)
        {
            isExportable = exportable[dataFormat];
        }

        if(isExportable && dataType)
        {
            isExportable = exportable.dataTypes.includes(dataType);
        }

        const changeFunction = (typeof onSelect !== 'undefined' && isExportable) ? (event) => { onSelect(event, data, !selected) } : null;

        return <div className={classNames('ListItem FieldListItem', ! isExportable && 'Disabled')} onClick={changeFunction}>
            {selectable && <div className="FieldCheckbox">
                {isExportable && <Checkbox
                    value={selected}
                    required={false}
                    name={id}
                    onChange={() => {}}
                />}
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
        </div>;
    }
}