import React, { Component } from 'react';

import FormItem from '../FormItem';
import Button from '@mui/material/Button';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';
import { UserType } from 'types/UserType';
import * as H from 'history';
import queryString from 'query-string';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface PersonFormProps extends ComponentWithNotifications {
    user: UserType;
    history: H.History;
};

type PersonFormState = {
    validation: any;
};

class PersonForm extends Component<PersonFormProps, PersonFormState> {
    constructor(props) {
        super(props);

        this.state = {
            validation: {},
        };
    }

    parseUserDetails = (user: UserType) => {
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
            details.middleName = user.details.middleName ?? '';
            details.lastName = user.details.lastName;
            details.email = user.details.email;
        }

        return details;
    };

    handleSubmit = (values, { setSubmitting }) => {
        const { history, notifications } = this.props;

        apiClient
            .post('/api/user', values)
            .then(response => {
                const params = queryString.parse(location.search);

                history.push({
                    pathname: typeof params.origin !== 'undefined' && params.origin !== null ? (params.origin as string) : '/',
                });

                setSubmitting(false);
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    notifications.show('An error occurred while updating your details', { variant: 'error' });
                }

                setSubmitting(false);
            });
    };

    render() {
        const { validation } = this.state;
        const { user } = this.props;

        let initialValues = this.parseUserDetails(user);

        const validationSchema = Yup.object().shape({
            ...(user.wizards.details && {
                firstName: Yup.string().required('Please enter a first name'),
                middleName: Yup.string().nullable(),
                lastName: Yup.string().required('Please enter a last name'),
            }),
            ...(user.wizards.email && {
                email: Yup.string().email('Please enter a valid email address').required('Please enter an email address'),
            }),
        });

        return (
            <Formik initialValues={initialValues} validationSchema={validationSchema} onSubmit={this.handleSubmit}>
                {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues }) => {
                    return (
                        <Form>
                            {user.wizards.details && (
                                <>
                                    <FormItem label="First Name">
                                        <Field component={Input} name="firstName" serverError={validation} />
                                    </FormItem>
                                    <FormItem label="Middle Name">
                                        <Field component={Input} name="middleName" serverError={validation} />
                                    </FormItem>
                                    <FormItem label="Last Name">
                                        <Field component={Input} name="lastName" serverError={validation} />
                                    </FormItem>
                                </>
                            )}

                            {user.wizards.email && (
                                <FormItem label="Email address">
                                    <Field component={Input} name="email" serverError={validation} />
                                </FormItem>
                            )}

                            <Button type="submit" disabled={isSubmitting}>
                                Save details
                            </Button>
                        </Form>
                    );
                }}
            </Formik>
        );
    }
}

export default withNotifications(PersonForm);