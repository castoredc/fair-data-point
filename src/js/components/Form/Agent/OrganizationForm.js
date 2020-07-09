import React, {Component} from 'react';

import '../Form.scss'
import FormItem from "../FormItem";
import Input from "../../Input";
import Dropdown from "../../Input/Dropdown";
import {mergeData} from "../../../util";

export default class OrganizationForm extends Component {
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
        const {countries} = this.props;
        const {data} = this.state;

        const required = "This field is required";
        const invalid = "This value is invalid";

        return (
            <div className="Organization">
                <FormItem label="Name">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="name"
                        onChange={this.handleChange}
                        value={data.name}
                    />
                </FormItem>
                <FormItem label="Department(s)">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="department"
                        onChange={this.handleChange}
                        value={data.department}
                    />
                </FormItem>
                <FormItem label="City">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="city"
                        onChange={this.handleChange}
                        value={data.city}
                    />
                </FormItem>
                <FormItem label="Country">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={countries}
                        name="country"
                        onChange={(e) => {
                            this.handleChange({target: {name: 'country', value: e.value}})
                        }}
                        value={countries.filter(({value}) => value === data.country)}
                    />
                </FormItem>
            </div>
        );
    }
}

const defaultData = {
    id:                    null,
    name:                  '',
    country:               '',
    city:                  '',
    department:            '',
    additionalInformation: '',
    coordinatesLatitude:   '',
    coordinatesLongitude:  '',
};