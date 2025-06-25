import React from 'react';
import './FieldListItem.scss';
import { FieldIcons } from '../Icon/FieldIcons';
import { classNames } from '../../util';
import { Icon } from '@castoredc/matter';

interface Exportable {
    exportable: boolean;
    dataTypes: string[];
    [key: string]: any;
}

interface FieldListItemProps {
    id: string;
    type: string;
    label: string;
    stepNumber: number;
    number: number;
    variableName: string;
    selected?: boolean;
    onSelect: (id: string, variableName: string, label: string) => void;
    exportable: Exportable;
    dataFormat?: string;
    dataType?: string;
    dataTransformation?: boolean;
}

const FieldListItem: React.FC<FieldListItemProps> = ({
    id,
    type,
    label,
    stepNumber,
    number,
    variableName,
    selected,
    onSelect,
    exportable,
    dataFormat,
    dataType,
    dataTransformation,
}) => {
    let isExportable = exportable.exportable;

    if (isExportable && dataTransformation) {
        isExportable = true;
    } else if (isExportable && dataType) {
        isExportable = exportable.dataTypes.includes(dataType);
    } else if (isExportable && dataFormat) {
        isExportable = exportable[dataFormat];
    }

    return (
        <div
            className={classNames('ListItem FieldListItem', !isExportable && 'Disabled', selected && 'Selected')}
            onClick={isExportable ? () => onSelect(id, variableName, label) : undefined}
        >
            <div className="FieldNumber">
                {stepNumber}.{number}
            </div>
            <div className="FieldIcon">
                <Icon type={FieldIcons[type]} height="12" width="12" />
            </div>
            <div className="FieldLabel">
                {label}
                <span className="FieldVariableName">{variableName}</span>
            </div>
        </div>
    );
};

export default FieldListItem;
