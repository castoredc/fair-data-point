import React from 'react';
import { classNames } from '../../util';

interface MetadataItemContainerProps {
    label: string;
    className?: string;
    table?: boolean;
    children: React.ReactNode;
    hideLabel?: boolean;
}

const MetadataItemContainer: React.FC<MetadataItemContainerProps> = ({ label, className, table, children, hideLabel = false }) => {
    return (
        <div className={classNames('MetadataItem', className, table && 'Table')}>
            {!hideLabel && <div className="MetadataItemLabel">{label}</div>}
            <div className="MetadataItemValue">{children}</div>
        </div>
    );
};

export default MetadataItemContainer;
