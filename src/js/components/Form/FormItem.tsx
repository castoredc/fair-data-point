import React, { FC } from 'react';
import { classNames } from '../../util';
import { FormLabel, Tooltip } from '@castoredc/matter';
import { InfoIcon } from '@castoredc/matter-icons';

interface FormItemProps {
    label?: string;
    children: React.ReactNode;
    hidden?: boolean;
    inline?: boolean;
    align?: string;
    className?: string;
    tooltip?: string;
    details?: string;
    isRequired?: boolean;
}

const FormItem: FC<FormItemProps> = ({ label, children, hidden, inline, align, className, tooltip, details, isRequired }) => {
    let alignClass = '';

    if (align === 'left') {
        alignClass = 'AlignLeft';
    } else if (align === 'center') {
        alignClass = 'AlignCenter';
    } else if (align === 'right') {
        alignClass = 'AlignRight';
    }

    return (
        <div className={classNames('FormItem', inline && alignClass, hidden && 'Hidden', inline && 'Inline', className)}>
            {label && (
                <div className="FormItemLabel">
                    <FormLabel>
                        {label}
                        {isRequired && (
                            <Tooltip content="This field is required">
                                <span className="RequiredIndicator">*</span>
                            </Tooltip>
                        )}
                        {tooltip && (
                            <>
                                &nbsp;
                                <Tooltip content={tooltip}>
                                    <InfoIcon />
                                </Tooltip>
                            </>
                        )}
                    </FormLabel>
                    {details && <div className="FormItemDetails">{details}</div>}
                </div>
            )}
            <div className="FormItemContent">{children}</div>
        </div>
    );
};

export default FormItem;
