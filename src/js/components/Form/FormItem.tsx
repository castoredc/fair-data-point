import React, {FC} from 'react';
import {classNames} from "../../util";
import {FormLabel} from "@castoredc/matter";

interface FormItemProps {
    label?: string,
    children: React.ReactNode,
    hidden?: boolean,
    inline?: boolean,
    align?: string,
    className?: string,
}

const FormItem: FC<FormItemProps> = ({label, children, hidden, inline, align, className}) => {
    let alignClass = '';

    if (align === 'left') {
        alignClass = 'AlignLeft';
    } else if (align === 'center') {
        alignClass = 'AlignCenter';
    } else if (align === 'right') {
        alignClass = 'AlignRight';
    }

    return (
        <div
            className={classNames('FormItem', inline && alignClass, hidden && 'Hidden', inline && 'Inline', className)}>
            {label && <div className="FormItemLabel">
                <FormLabel>{label}</FormLabel>
            </div>}
            <div className="FormItemContent">
                {children}
            </div>
        </div>
    );
}

export default FormItem;
