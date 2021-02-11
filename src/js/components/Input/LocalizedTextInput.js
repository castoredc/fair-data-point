import React, {Component} from 'react';

import './Input.scss'
import {Button, TextStyle} from "@castoredc/matter";
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

    addInput = () => {
        const { inputData } = this.state;

        const newInputData = inputData;
        newInputData.push(defaultData);

        this.setState({
            inputData: newInputData,
        });
    };

    removeInput = (index) => {
        const { inputData } = this.state;

        let newInputData = inputData;
        newInputData.splice(index, 1);

        this.setState({
            inputData: newInputData,
        });
    };

    render() {
        const {validators, errorMessages, languages, serverError, as, rows, name} = this.props;
        const { inputData } = this.state;

        const count = inputData.length;

        return (
            <div className="Input LocalizedTextInput">
                <div className="LocalizedTextInputItems">
                    {inputData.map((localizedTextItem, index) => {
                        const last = count === index + 1;
                        const first = index === 0;

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
                            <div className="LocalizedTextInputButtons">
                                <div className="LocalizedTextInputButton">
                                    {!first && <Button icon="cross" className="RemoveButton" buttonType="contentOnly" onClick={() => this.removeInput(index)} iconDescription="Remove text" />}
                                </div>
                            </div>
                        </div>
                    })}
                </div>

                <div className="LocalizedTextInputAddButton">
                    <Button icon="add" className="AddButton" buttonType="contentOnly" onClick={this.addInput}>Add new</Button>
                </div>

                {serverError && serverError.map((errorText, index) => (
                    <TextStyle key={index} variation="error">
                        {errorText}
                    </TextStyle>
                ))}
            </div>
        );
    }
}

const defaultData = {
    text:     '',
    language: null
};