import React, {Component} from 'react';

import '../Form.scss'
import FormItem from "../FormItem";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import {PersonType} from "../../../types/PersonType";
import {Field, Form, Formik} from 'formik';
import Input from "components/Input/Formik/Input";
import * as Yup from 'yup';
import {Button} from "@castoredc/matter";
import {FormikHelpers} from "formik/dist/types";

type PersonFormProps = {
    email?: string,
    studyId?: string,
    handleSubmit: (values: any, formikHelpers: FormikHelpers<any>) => void,
}

type PersonFormState = {
    checkedForExistingEmail: boolean,
    hasExistingEmail: boolean,
    isLoading: boolean,
    initialValues: PersonType,
    validation: any
}

export default class PersonForm extends Component<PersonFormProps, PersonFormState> {
    private timer: null | ReturnType<typeof setTimeout> = null;

    constructor(props) {
        super(props);

        this.state = {
            checkedForExistingEmail: false,
            hasExistingEmail: false,
            isLoading: false,
            initialValues: defaultData,
            validation: {}
        };

        this.timer = null;
    }

    componentDidMount() {
        const {email} = this.props;

        if (email) {
            this.getPersonInformation(email);
        } else {
            this.setState({
                initialValues: defaultData,
            });
        }
    }

    getPersonInformation = (email: string, callback?: (data: PersonType) => void) => {
        axios.get<PersonType>('/api/agent/person/email', {params: {email: email}}).then((response) => {
            const initialValues = response.data;
            this.setState({
                initialValues,
                isLoading: false,
                checkedForExistingEmail: true,
                hasExistingEmail: true
            }, () => {
                callback && callback(initialValues);
            });
        })
            .catch((error) => {
                const initialValues = {
                    ...defaultData,
                    email: email
                };

                if (error.response && error.response.status === 404) {
                    this.setState({
                        isLoading: false,
                        checkedForExistingEmail: true,
                        hasExistingEmail: false,
                        initialValues
                    }, () => {
                        callback && callback(initialValues);
                    });
                } else {
                    this.setState({
                        isLoading: false
                    }, () => {
                        callback && callback(initialValues);
                    });

                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    handleEmailChange = (event, setValues) => {
        if (this.timer) {
            clearTimeout(this.timer);
        }

        const email = event.target.value;

        Yup.string().required().email().isValid(email).then((valid) => {
            if (valid) {
                this.timer = setTimeout(() => {
                    this.getPersonInformation(email, (data: PersonType) => {
                        setValues({
                            id: data.id,
                            firstName: data.firstName,
                            middleName: data.middleName,
                            lastName: data.lastName,
                            email: data.email,
                            orcid: data.orcid,
                        })
                    });
                }, 600);
            }
        });
    };

    render() {
        const {checkedForExistingEmail, hasExistingEmail, initialValues, isLoading, validation} = this.state;
        const {email, handleSubmit} = this.props;

        const edit = !!email;

        return (
            <Formik
                initialValues={initialValues}
                validationSchema={PersonSchema}
                onSubmit={handleSubmit}
            >
                {({
                      values,
                      errors,
                      touched,
                      handleChange,
                      handleBlur,
                      handleSubmit,
                      isSubmitting,
                      setValues
                  }) => {
                    return <Form>
                        <FormItem label="Email address">
                            <Field component={Input}
                                   name="email"
                                   readOnly={edit}
                                // autoFocus
                                   onChange={(event) => this.handleEmailChange(event, setValues)}
                                   serverError={validation}
                            />
                        </FormItem>

                        {/*{edit && <Alert*/}
                        {/*    variant="info"*/}
                        {/*    icon="info"*/}
                        {/*    form*/}
                        {/*>*/}
                        {/*    The details of this person can only be edited by {name}.*/}
                        {/*    Please contact {name} or Castor Support to update these details.*/}
                        {/*</Alert>}*/}

                        {/*{(!edit && hasExistingEmail) && <Alert*/}
                        {/*    variant="info"*/}
                        {/*    icon="info"*/}
                        {/*    form*/}
                        {/*>*/}
                        {/*    The details of this person were automatically populated.*/}
                        {/*    Please contact {name} or Castor Support to update these details, if needed.*/}
                        {/*</Alert>}*/}

                        {(checkedForExistingEmail && !hasExistingEmail) && <div className="ContactDetails">
                            <FormItem label="First Name">
                                <Field component={Input} name="firstName" serverError={validation}/>
                            </FormItem>
                            <FormItem label="Middle Name">
                                <Field component={Input} name="middleName" serverError={validation}/>
                            </FormItem>
                            <FormItem label="Last Name">
                                <Field component={Input} name="lastName" serverError={validation}/>
                            </FormItem>
                            <FormItem label="ORCID">
                                <Field component={Input} name="orcid" serverError={validation}/>
                            </FormItem>
                        </div>}

                        <Button buttonType="primary" type="submit"
                                disabled={!checkedForExistingEmail || isLoading || isSubmitting}>Add person</Button>
                    </Form>
                }}
            </Formik>
        );
    }
}

const defaultData = {
    id: null,
    firstName: '',
    middleName: '',
    lastName: '',
    email: '',
    orcid: '',
};

const PersonSchema = Yup.object().shape({
    firstName: Yup.string().required('Please enter a first name'),
    middleName: Yup.string().nullable(),
    lastName: Yup.string().required('Please enter a last name'),
    email: Yup.string().email('Please enter a valid email address').required('Please enter an email address'),
    orcid: Yup.string().matches(/^\d{4}(-)\d{4}(-)\d{4}(-)\d{3}[\dX]$/i, 'Please enter a valid ORCID').nullable(),
});
