import React, {Component} from 'react';
import Form from 'react-bootstrap/Form'
import {classNames} from "../../util";
import {FormLabel} from "@castoredc/matter";

class FormItem extends Component {
    render() {
        const {label, children, hidden} = this.props;
        return (
            <div className={classNames('FormItem', hidden && 'Hidden', this.props.classNames)}>
                <Form.Row>
                    <FormLabel>{label}</FormLabel>
                </Form.Row>
                <Form.Row>
                    {children}
                </Form.Row>
            </div>
        );
    }
}

export default FormItem;
