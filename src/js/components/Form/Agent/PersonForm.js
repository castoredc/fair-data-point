import React, {Component} from 'react';
import {ValidatorForm} from 'react-form-validator-core';

import '../Form.scss'
import FormItem from "../FormItem";
import Input from "../../Input";
import {classNames, mergeData} from "../../../util";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import Alert from "../../Alert";

export default class PersonForm extends Component {
    constructor(props) {
        super(props);

        const newData = props.data ? mergeData(defaultData, props.data) : defaultData;

        this.state = {
            data: newData,
            checkedForExistingEmail: false,
            hasExistingEmail: false
        };

        if(newData.email !== '') {
            this.getPersonInformation(newData.email);
        }

        this.timer = null;
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

    getPersonInformation = (email) => {
        const {handleDataChange} = this.props;

        axios.get('/api/agent/person/email', {params: {email: email}}).then((response) => {
            console.log(response.data);
            const newData = mergeData(defaultData, response.data);

            this.setState({
                data: newData,
                isLoading: false,
                checkedForExistingEmail: true,
                hasExistingEmail: true
            }, () => {
                handleDataChange(newData);
            });
        })
            .catch((error) => {
                if (error.response && error.response.status === 404) {
                    const newData = {
                        ...defaultData,
                        email: email
                    };

                    this.setState({
                        isLoading: false,
                        checkedForExistingEmail: true,
                        hasExistingEmail: false,
                        data: newData
                    }, () => {
                        handleDataChange(newData);
                    });

                } else {
                    this.setState({
                        isLoading: false
                    });

                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

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

    handleEmailChange = (event) => {
        this.handleChange(event);

        const email = event.target.value;

        clearTimeout(this.timer);
        this.timer = setTimeout(() => {
            const validation = ValidatorForm.getValidator('isEmail', email, true);

            if(validation === true) {
                this.getPersonInformation(email);
            }
        }, 600);
    };

    render() {
        const {data, checkedForExistingEmail, hasExistingEmail} = this.state;
        const {edit} = this.props;

        const required = "This field is required";
        const invalid = "This value is invalid";

        const readOnly = !checkedForExistingEmail || (checkedForExistingEmail && hasExistingEmail);

        const name = [data.firstName, data.middleName, data.lastName].filter(Boolean).join(' ');

        return (
            <div className="Contact">
                <FormItem label="Email address">
                    <Input
                        validators={['required', 'isEmail']}
                        errorMessages={[required, invalid]}
                        name="email"
                        onChange={this.handleEmailChange}
                        value={data.email}
                        readOnly={edit}
                    />
                </FormItem>


                {edit && <Alert
                    variant="info"
                    icon="info"
                    form
                >
                    The details of this publisher can only be edited by {name}.
                    Please contact {name} or Castor Support to update these details.
                </Alert>}

                {(! edit && hasExistingEmail) && <Alert
                    variant="info"
                    icon="info"
                    form
                >
                    The details of this publisher were automatically populated.
                    Please contact {name} or Castor Support to update these details, if needed.
                </Alert>}

                <div className={classNames('ContactDetails', !checkedForExistingEmail && 'NotLoaded')}>
                    <FormItem label="First Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="firstName"
                            onChange={this.handleChange}
                            value={data.firstName}
                            readOnly={readOnly}
                        />
                    </FormItem>
                    <FormItem label="Middle Name">
                        <Input
                            name="middleName"
                            onChange={this.handleChange}
                            value={data.middleName}
                            readOnly={readOnly}
                        />
                    </FormItem>
                    <FormItem label="Last Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="lastName"
                            onChange={this.handleChange}
                            value={data.lastName}
                            readOnly={readOnly}
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
                            readOnly={readOnly}
                        />
                    </FormItem>
                </div>
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