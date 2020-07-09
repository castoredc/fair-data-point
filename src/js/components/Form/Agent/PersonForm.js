import React, {Component} from 'react';
import {ValidatorForm} from 'react-form-validator-core';

import '../Form.scss'
import FormItem from "../FormItem";
import Input from "../../Input";
import {mergeData} from "../../../util";

export default class PersonForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: props.data ? mergeData(defaultData, props.data) : defaultData,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show, data} = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                data: data ? mergeData(defaultData, data) : defaultData,
            })
        }
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isOrcid', (value) => {
            if (value === '' || value === null) {
                return true;
            }

            const regex = /^\d{4}(-)\d{4}(-)\d{4}(-)\d{3}[\dX]$/i;
            return regex.test(value);
        });
    }

    handleChange = (event) => {
        const {data} = this.state;
        const {handleDataChange} = this.props;

        const newState = {
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
        };

        this.setState(newState);
        handleDataChange(newState.data);
    };

    render() {
        const {data} = this.state;

        const required = "This field is required";
        const invalid = "This value is invalid";

        return (
            <div className="Contact">
                <FormItem label="First Name">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="firstName"
                        onChange={this.handleChange}
                        value={data.firstName}
                    />
                </FormItem>
                <FormItem label="Middle Name">
                    <Input
                        name="middleName"
                        onChange={this.handleChange}
                        value={data.middleName}
                    />
                </FormItem>
                <FormItem label="Last Name">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="lastName"
                        onChange={this.handleChange}
                        value={data.lastName}
                    />
                </FormItem>
                <FormItem label="Email address">
                    <Input
                        validators={['required', 'isEmail']}
                        errorMessages={[required, invalid]}
                        name="email"
                        onChange={this.handleChange}
                        value={data.email}
                    />
                </FormItem>
                <FormItem label="ORCID">
                    <Input
                        placeholder="0000-0000-0000-0000"
                        validators={['isOrcid']}
                        errorMessages={[invalid]}
                        name="orcid"
                        onChange={this.handleChange}
                        value={data.orcid}
                        mask={[/\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /\d/, '-', /\d/, /\d/, /\d/, /[\dX]/]}
                    />
                </FormItem>

            </div>
        );
    }
}

const defaultData = {
    id:         null,
    firstName:  '',
    middleName: '',
    lastName:   '',
    email:      '',
    orcid:      '',
};