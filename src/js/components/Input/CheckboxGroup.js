import React, {Component} from 'react';
import Form from 'react-bootstrap/Form'

import './Checkbox.scss';
import './Input.scss'
import {ChoiceOption} from "@castoredc/matter";

export default class CheckboxGroup extends Component {
    constructor(props) {
        super(props);

        this.state = {
            selection: []
        };
    }

    onCheckboxChange = (e) => {
        const { onChange, name } = this.props;
        const { selection } = this.state;

        let newSelection = selection;

        if(e.target.checked) {
            newSelection.push(e.target.value);
        } else {
            newSelection = newSelection.filter(function(option) { return option !== e.target.value });
        }

        this.setState({
            selection: newSelection
        }, () => {
           onChange({target: { name: name, value: newSelection}});
        });
    };

    render() {
        const {
            options,
            value
          } = this.props;

        return (
            <Form.Group className="CheckboxFormGroup" ref={(r) => { this.input = r; }}>
                {options.map((option) => {
                    return <ChoiceOption
                        key={option.value}
                        labelText={option.label}
                        type="checkbox"
                        onChange={this.onCheckboxChange}
                        value={option.value}
                        checked={value.find((valueOption) => {return valueOption === option.value}) || false}
                    />
                })}
            </Form.Group>
        );
    }
}