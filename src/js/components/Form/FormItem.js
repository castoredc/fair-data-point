import React, {Component} from 'react';
import Form from 'react-bootstrap/Form'
import {classNames} from "../../util";
import {FormLabel} from "@castoredc/matter";

class FormItem extends Component {
    render() {
        const {label, children, hidden, inline, align} = this.props;

        let alignClass = '';

        if(align === 'left') {
            alignClass = 'AlignLeft';
        } else if(align === 'center') {
            alignClass = 'AlignCenter';
        } else if(align === 'right') {
            alignClass = 'AlignRight';
        }

        return (
            <div className={classNames('FormItem', inline && alignClass, hidden && 'Hidden', inline && 'Inline', this.props.classNames)}>
                <div className="FormItemLabel">
                    <FormLabel>{label}</FormLabel>
                </div>
                <div className="FormItemContent">
                    {children}
                </div>
            </div>
        );
    }
}

export default FormItem;
