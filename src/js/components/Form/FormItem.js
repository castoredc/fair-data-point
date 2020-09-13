import React, {Component} from 'react';
import {classNames} from "../../util";
import {FormLabel} from "@castoredc/matter";

class FormItem extends Component {
    render() {
        const {label, children, hidden, inline, align, className} = this.props;

        let alignClass = '';

        if(align === 'left') {
            alignClass = 'AlignLeft';
        } else if(align === 'center') {
            alignClass = 'AlignCenter';
        } else if(align === 'right') {
            alignClass = 'AlignRight';
        }

        return (
            <div className={classNames('FormItem', inline && alignClass, hidden && 'Hidden', inline && 'Inline', className)}>
                {label && <div className="FormItemLabel">
                    <FormLabel>{label}</FormLabel>
                </div>}
                <div className="FormItemContent">
                    {children}
                </div>
            </div>
        );
    }
}

export default FormItem;
