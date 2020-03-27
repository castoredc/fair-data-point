import React, {Component} from 'react';
import Form from 'react-bootstrap/Form'
import {classNames} from "../../util";

class FormItem extends Component {
    render() {
        const {label, children, hidden} = this.props;
        return (
            <div className={classNames('FormItem', hidden && 'Hidden', this.props.classNames)}>
                <Form.Row>
                    <Form.Label className="FormLabel">{label}</Form.Label>
                </Form.Row>
                <Form.Row>
                    {children}
                </Form.Row>
            </div>
        );
    }
}

export default FormItem;
