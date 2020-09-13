import React, {Component} from 'react';
import {Heading} from "@castoredc/matter";

class FormHeading extends Component {
    render() {
        const {label} = this.props;
        return (
            <div className="FormHeading">
                <Heading type="Subsection">{label}</Heading>
            </div>
        );
    }
}

export default FormHeading;
