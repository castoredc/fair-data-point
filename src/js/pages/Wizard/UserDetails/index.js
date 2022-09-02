import React, { Component } from 'react';
import './UserDetails.scss';
import Emoji from '../../../components/Emoji';
import { Button, Stack } from '@castoredc/matter';
import FormItem from '../../../components/Form/FormItem';
import Input from '../../../components/Input';
import { ValidatorForm } from 'react-form-validator-core';
import { toast } from 'react-toastify';
import ToastContent from '../../../components/ToastContent';
import queryString from 'query-string';
import { apiClient } from 'src/js/network';

export default class UserDetails extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: this.parseUserDetails(props.user),
            isLoading: false,
            isSaved: false,
        };
    }

    parseUserDetails = user => {
        let details = {
            firstName: '',
            middleName: '',
            lastName: '',
            email: '',
        };

        if (typeof user.suggestions.details !== 'undefined') {
            details.firstName = user.suggestions.details.firstName;
            details.lastName = user.suggestions.details.lastName;
        }

        if (user.details !== null) {
            details.firstName = user.details.firstName;
            details.middleName = user.details.middleName;
            details.lastName = user.details.lastName;
            details.email = user.details.email;
        }

        return details;
    };

    handleChange = event => {
        const { data } = this.state;

        const newState = {
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
        };

        this.setState(newState);
    };

    handleSubmit = event => {
        const { data } = this.state;

        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({
                isLoading: true,
            });

            apiClient
                .post('/api/user', data)
                .then(response => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                    });
                })
                .catch(error => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred while updating your details" />, {
                            position: 'top-center',
                        });
                    }
                    this.setState({
                        isLoading: false,
                    });
                });
        }

        return false;
    };

    render() {
        const { user, location } = this.props;
        const { data, isLoading, isSaved } = this.state;

        const required = 'This field is required';
        const invalid = 'This value is invalid';

        if (isSaved) {
            const params = queryString.parse(location.search);
            window.location.href = typeof params.origin !== 'undefined' ? params.origin : '/';
        }

        return (
            <>
                <header>
                    <h1>
                        <Emoji symbol="👋" />
                        &nbsp; Hi {data.firstName}!
                    </h1>
                    <div className="Description">Before you continue, please check your details below.</div>
                </header>

                <ValidatorForm className="FullHeightForm" ref={node => (this.form = node)} onSubmit={this.handleSubmit} method="post">
                    {user.wizards.details && (
                        <>
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
                                <Input name="middleName" onChange={this.handleChange} value={data.middleName} />
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
                        </>
                    )}

                    {user.wizards.email && (
                        <FormItem label="Email address">
                            <Input
                                validators={['required', 'isEmail']}
                                errorMessages={[required, invalid]}
                                name="email"
                                onChange={this.handleChange}
                                value={data.email}
                            />
                        </FormItem>
                    )}

                    <div className="FormButtons">
                        <Stack distribution="trailing">
                            <Button type="submit" disabled={isLoading}>
                                Save details
                            </Button>
                        </Stack>
                    </div>
                </ValidatorForm>
            </>
        );
    }
}
