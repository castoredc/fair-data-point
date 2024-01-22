import React, { Component } from 'react';

import '../Form.scss';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button, Stack } from '@castoredc/matter';
import FormItem from './../FormItem';
import { mergeData } from '../../../util';
import * as H from 'history';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import SingleChoice from 'components/Input/Formik/SingleChoice';
import * as Yup from 'yup';
import { apiClient } from 'src/js/network';

interface CatalogFormProps {
    catalog?: any;
    history: H.History;
}

interface CatalogFormState {
    initialValues: any;
    update: boolean;
    validation?: any;
}

export default class CatalogForm extends Component<CatalogFormProps, CatalogFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.catalog ? mergeData(defaultData, props.catalog) : defaultData,
            validation: {},
            update: !!props.catalog,
        };
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { catalog, history } = this.props;

        apiClient
            .post('/api/catalog' + (catalog ? '/' + catalog.slug : ''), values)
            .then(response => {
                setSubmitting(false);

                if (catalog) {
                    history.push('/dashboard/catalogs/' + values.slug);
                    toast.success(<ToastItem type="success" title="The catalog details are saved successfully" />, {
                        position: 'top-right',
                    });
                } else {
                    history.push('/dashboard/catalogs/' + response.data.slug + '/metadata');
                }
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    render() {
        const { initialValues, validation } = this.state;
        const { catalog } = this.props;

        return (
            <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={CatalogSchema}>
                {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                    return (
                        <Form>
                            <div className="FormContent">
                                <FormItem label="Slug" tooltip="The unique identifying part of a web address, typically at the end of the URL">
                                    <Field component={Input} name="slug" serverError={validation} />
                                </FormItem>

                                <FormItem>
                                    <Field
                                        component={SingleChoice}
                                        labelText="Accepts submissions"
                                        name="acceptSubmissions"
                                        details="When selected, others will be able to add their study to this catalog"
                                    />
                                </FormItem>

                                {/*{values.acceptSubmissions && (*/}
                                {/*    <FormItem>*/}
                                {/*        <Field*/}
                                {/*            component={SingleChoice}*/}
                                {/*            labelText="Data will be accessed during submission"*/}
                                {/*            name="submissionAccessesData"*/}
                                {/*        />*/}
                                {/*    </FormItem>*/}
                                {/*)}*/}
                            </div>

                            {catalog ? (
                                <div className="FormButtons">
                                    <Stack distribution="trailing">
                                        <Button disabled={isSubmitting} type="submit">
                                            Update catalog
                                        </Button>
                                    </Stack>
                                </div>
                            ) : (
                                <footer>
                                    <Button disabled={isSubmitting} type="submit">
                                        Add catalog
                                    </Button>
                                </footer>
                            )}
                        </Form>
                    );
                }}
            </Formik>
        );
    }
}

export const defaultData = {
    slug: '',
    acceptSubmissions: false,
    submissionAccessesData: false,
};

const CatalogSchema = Yup.object().shape({
    slug: Yup.string().required('Please enter a slug'),
    acceptSubmissions: Yup.boolean().required('Please enter if this catalog accepts submissions'),
    submissionAccessesData: Yup.boolean().when('acceptSubmissions', {
        is: true,
        then: Yup.boolean().required('Please enter if the submission accesses data in the process'),
    }),
});
