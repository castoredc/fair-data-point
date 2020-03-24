import React, { Component } from 'react';
import Form from 'react-bootstrap/Form'

class FormHeading extends Component {
    render() {
        const {label} = this.props;
        return (
            <div className="FormHeading">
                <Form.Row>
                    <h2>{label}</h2>
                </Form.Row>
            </div>
        );
    }
}

export default FormHeading;
