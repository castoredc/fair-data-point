import React, {Component} from 'react';

import '../Form.scss'
import FormItem from "../FormItem";
import {Button, Stack} from "@castoredc/matter";
import axios, {CancelTokenSource} from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import {classNames} from "../../../util";
import * as Yup from "yup";
import {Field, Form, Formik} from "formik";
import Select, {AsyncSelect, OptionType} from "components/Input/Formik/Select";
import {OrganizationType} from "../../../types/OrganizationType";
import {ActionMeta, OptionsType, ValueType} from "react-select/src/types";
import debounce from 'lodash.debounce';
import Input from "components/Input/Formik/Input";
import {FormikHelpers} from "formik/dist/types";


type OrganizationFormProps = {
    countries: any,
    handleSubmit: (values: any, formikHelpers: FormikHelpers<any>) => void,
}

type OrganizationFormState = {
    isLoading: boolean,
    initialValues: OrganizationType,
    validation: any,
    axiosCancel: CancelTokenSource | null,
    defaultOptions: any,
    cachedOptions: OptionsType<OptionType>,
}

export default class OrganizationForm extends Component<OrganizationFormProps, OrganizationFormState>  {
    private timeout: null | ReturnType<typeof setTimeout> = null;

    constructor(props) {
        super(props);

        this.state = {
            axiosCancel: null,
            isLoading: false,
            initialValues: defaultData,
            validation: {},
            defaultOptions: [],
            cachedOptions: [],
        };

        this.loadOrganizations = debounce(this.loadOrganizations, 1000)
    }

    loadOrganizations = (country: OptionType, input: string, callback: (options) => void) => {
        const {axiosCancel} = this.state;

        if (axiosCancel !== null) {
            axiosCancel.cancel();
        }

        const CancelToken = axios.CancelToken;
        const source = CancelToken.source();

        this.setState({
            axiosCancel: source,
        });

        axios.get('/api/agent/organization', {
            cancelToken: source.token,
            params: {
                country: country.value,
                search: input,
            },
        }).then((response) => {
            this.setState({
                cachedOptions: response.data
            }, () => {
                callback(response.data);
            });
        })
            .catch((error) => {
                if (!axios.isCancel(error)) {
                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>);
                    }
                }
                callback(null);
            });
    };

    handleOrganizationSelect = (organization) => {
        return {
            source: organization.source,
            id: organization.value,
            name: organization.label,
            city: organization.data.city,
        };
    };

    render() {
        const {countries, handleSubmit} = this.props;
        const {initialValues, defaultOptions, validation, cachedOptions} = this.state;

        return (
            <Formik
                initialValues={initialValues}
                validationSchema={OrganizationSchema}
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
                      setValues,
                        setFieldValue
                  }) => {

                    const showForm = values.source === 'manual';

                    const toggleManual = () => {
                        setValues({
                            ...defaultData,
                            country: values.country,
                            source: values.source === 'manual' ? null : 'manual',
                            id: null,
                        });
                    };

                    return <Form>
                        <FormItem label="Country">
                            <Field
                                component={Select}
                                options={countries}
                                name="country"
                                menuPosition="fixed"
                                serverError={validation}
                            />
                        </FormItem>
                        <div className={classNames(values.country === null && 'WaitingOnInput')}>
                            <Stack>
                                {!showForm && <FormItem label="Organization / Institution">
                                    <Field
                                        component={AsyncSelect}
                                        name="organization"
                                        // onChange={this.handleOrganizationChange}
                                        loadOptions={(value, callback) => this.loadOrganizations(values.country, value, callback)}
                                        cachedOptions={cachedOptions}
                                        menuPosition="fixed"
                                        isDisabled={values.country === '' || values.country === null}
                                        onChange={(value) => setValues({...values, ...this.handleOrganizationSelect(value)})}
                                        defaultOptions={defaultOptions}
                                        serverError={validation}
                                    />

                                    <Button buttonType="contentOnly" className="CannotFind" onClick={toggleManual}
                                            disabled={values.country === '' || values.country === null}>
                                        I cannot find my organization
                                    </Button>
                                </FormItem>}

                                {showForm && <>
                                    <FormItem label="Organization / Institution Name">
                                        <Field
                                            component={Input}
                                            name="name"
                                            serverError={validation}
                                            autoFocus
                                        />

                                        <Button buttonType="contentOnly" className="CannotFind"
                                                onClick={toggleManual}
                                                disabled={values.country === '' || values.country === null}>
                                            Search for an organization
                                        </Button>
                                    </FormItem>
                                    <FormItem label="City">
                                        <Field component={Input} name="city" serverError={validation} />
                                    </FormItem>
                                </>}
                            </Stack>

                            <Button buttonType="primary" type="submit" disabled={isSubmitting}>Add center</Button>
                        </div>
                    </Form>;
                }}
            </Formik>
        );
    }
}

const defaultData = {
    id: null,
    name: '',
    source: null,
    country: '',
    city: '',
    organization: {},
};

const OrganizationSchema = Yup.object().shape({
    source: Yup.string().required(),
    country: Yup.string().required("Please select a country"),
    name: Yup.string().when('source', {
        is: 'manual',
        then: Yup.string().required('Please enter a name'),
    }),
    city: Yup.string().when('source', {
        is: 'manual',
        then: Yup.string().required('Please enter a city'),
    }),
    id: Yup.string().nullable().when('source', {
        is: !'manual',
        then: Yup.string().required('Please select an organization'),
    }),
});