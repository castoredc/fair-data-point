import React, {Component} from 'react';
import Form from 'react-bootstrap/Form'
import {Heading} from "@castoredc/matter";

class FormHeading extends Component {
    render() {
        const {label} = this.props;
        return (
            <div className="FormHeading">
                <Form.Row>
                    <Heading type="Subsection">{label}</Heading>
                </Form.Row>
            </div>
        );
    }
}

export default FormHeading;
