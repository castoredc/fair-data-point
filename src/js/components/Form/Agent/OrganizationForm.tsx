import React, {Component} from 'react';

import '../Form.scss'
import FormItem from "../FormItem";
import {Button} from "@castoredc/matter";
import {classNames} from "../../../util";
import * as Yup from "yup";
import {Field, Form, Formik} from "formik";
import Select from "components/Input/Formik/Select";
import {FormikHelpers} from "formik/dist/types";
import OrganizationSelect from "components/Input/Formik/OrganizationSelect";


type OrganizationFormProps = {
    countries: any,
    handleSubmit: (values: any, formikHelpers: FormikHelpers<any>) => void,
}

type OrganizationFormState = {
    initialValues: any,
    validation: any,
}

export default class OrganizationForm extends Component<OrganizationFormProps, OrganizationFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: defaultData,
            validation: {},
        };
    }

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
        const {initialValues, validation} = this.state;

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
                  }) => {
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
                            <Field
                                component={OrganizationSelect}
                                country={values.country}
                                name="organization"
                                serverError={validation}
                            />
                            <Button buttonType="primary" type="submit" disabled={isSubmitting}>Add center</Button>
                        </div>
                    </Form>;
                }}
            </Formik>
        );
    }
}

const defaultData = {
    country: '',
    organization: {
        id: null,
        name: '',
        source: null,
        city: '',
    }
};

const OrganizationSchema = Yup.object().shape({
    country: Yup.string().required("Please select a country"),
    organization: Yup.object().shape({
        source: Yup.string().required('Please select an organization'),
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
    })
});