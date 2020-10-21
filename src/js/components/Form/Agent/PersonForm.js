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

        this.state = {
            checkedForExistingEmail: false,
            hasExistingEmail: false
        };

        if(props.data.email !== '') {
            this.getPersonInformation(props.data.email);
        }

        this.timer = null;
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show, data} = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                data: data
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
        const {data, handleDataChange} = this.props;

        axios.get('/api/agent/person/email', {params: {email: email}}).then((response) => {
            const newData = mergeData(data, response.data);

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
                        ...data,
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

    handleEmailChange = (event) => {
        const {handleChange} = this.props;
        handleChange(event);

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
        const {checkedForExistingEmail, hasExistingEmail} = this.state;
        const {data, edit, handleChange} = this.props;

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
                    The details of this person can only be edited by {name}.
                    Please contact {name} or Castor Support to update these details.
                </Alert>}

                {(! edit && hasExistingEmail) && <Alert
                    variant="info"
                    icon="info"
                    form
                >
                    The details of this person were automatically populated.
                    Please contact {name} or Castor Support to update these details, if needed.
                </Alert>}

                <div className={classNames('ContactDetails', !checkedForExistingEmail && 'NotLoaded')}>
                    <FormItem label="First Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="firstName"
                            onChange={handleChange}
                            value={data.firstName}
                            readOnly={readOnly}
                        />
                    </FormItem>
                    <FormItem label="Middle Name">
                        <Input
                            name="middleName"
                            onChange={handleChange}
                            value={data.middleName}
                            readOnly={readOnly}
                        />
                    </FormItem>
                    <FormItem label="Last Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="lastName"
                            onChange={handleChange}
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
                            onChange={handleChange}
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