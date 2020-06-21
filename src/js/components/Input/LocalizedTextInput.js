import React, {Component} from 'react';
import Form from 'react-bootstrap/Form'

import './Input.scss'
import {TextStyle} from "@castoredc/matter";
import Input from "./index";
import Dropdown from "./Dropdown";
import {replaceAt} from "../../util";

export default class LocalizedTextInput extends Component {
    constructor(props) {
        super(props);

        this.state = {
            inputData: props.value !== null ? props.value : [defaultData]
        };
    }

    handleChange = (index, e) => {
        const { name, onChange } = this.props;
        const { inputData } = this.state;

        const newData = replaceAt(inputData, index, {
            ...inputData[index],
            [e.target.name]: e.target.value
        });

        this.setState({
            inputData: newData,
        }, () => {
            onChange({target: { name: name, value: newData }});
        });
    };

    render() {
        const {validators, errorMessages, languages, serverError, as, rows, name} = this.props;
        const { inputData } = this.state;

        return (
            <Form.Group className="Input LocalizedTextInput">
                {inputData.map((localizedTextItem, index) => {
                    return <div key={`${name}-${index}`} className="LocalizedTextInputItem">
                        <div className="LocalizedTextInputText">
                            <Input
                                validators={validators}
                                errorMessages={errorMessages}
                                name="text"
                                onChange={(e) => {this.handleChange(index, e)}}
                                value={localizedTextItem.text}
                                as={as}
                                rows={rows}
                            />
                        </div>
                        <div className="LocalizedTextInputLanguage">
                            <Dropdown
                                validators={validators}
                                errorMessages={errorMessages}
                                options={languages}
                                name="language"
                                onChange={(e) => {this.handleChange(index, {target: { name: 'language', value: e.value }})}}
                                value={languages.filter((language) => language.value === localizedTextItem.language)}
                                width="minimum"
                            />
                        </div>
                    </div>
                })}

                {serverError && serverError.map((errorText, index) => (
                    <TextStyle key={index} variation="error">
                        {errorText}
                    </TextStyle>
                ))}
            </Form.Group>
        );
    }
}

const defaultData = {
    text:     '',
    language: null
};