import React, {Component} from 'react';

import '../Form.scss'
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import FormItem from "./../FormItem";
import {mergeData} from "../../../util";
import {Button, Stack} from "@castoredc/matter";
import * as H from "history";
import {Field, Form, Formik} from "formik";
import Input from "components/Input/Formik/Input";
import * as Yup from "yup";

interface DataModelFormProps {
    dataModel?: any,
    history: H.History;
}

interface DataModelFormState {
    initialValues: any,
    validation?: any,
}

export default class DataModelForm extends Component<DataModelFormProps, DataModelFormState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.dataModel ? mergeData(defaultData, props.dataModel) : defaultData,
            validation: {},
        };
    }

    handleSubmit = (values, {setSubmitting}) => {
        const {dataModel, history} = this.props;

        axios.post('/api/model' + (dataModel ? '/' + dataModel.id : ''), values)
            .then((response) => {
                setSubmitting(false);

                if (dataModel) {
                    toast.success(<ToastContent type="success"
                                                message="The data model details are saved successfully"/>, {
                        position: "top-right",
                    });
                } else {
                    history.push('/dashboard/data-models/' + response.data.id);
                }
            })
            .catch((error) => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>, {
                        position: "top-center",
                    });
                }
            });
    };

    render() {
        const {initialValues, validation} = this.state;
        const {dataModel} = this.props;

        return (
            <Formik
                initialValues={initialValues}
                onSubmit={this.handleSubmit}
                validationSchema={DataModelSchema}
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
                    return <Form>
                        <div className="FormContent">
                            <FormItem label="Title">
                                <Field
                                    component={Input}
                                    name="title"
                                    serverError={validation}
                                />
                            </FormItem>
                            <FormItem label="Description">
                                <Field
                                    component={Input}
                                    name="description"
                                    serverError={validation}
                                    multiline
                                />
                            </FormItem>
                        </div>

                        {dataModel ? <div className="FormButtons">
                            <Stack distribution="trailing">
                                <Button disabled={isSubmitting} type="submit">
                                    Update data model
                                </Button>
                            </Stack>
                        </div> : <footer>
                            <Button disabled={isSubmitting} type="submit">
                                Add data model
                            </Button>
                        </footer>}
                    </Form>;
                }}
            </Formik>
        );
    }
}

export const defaultData = {
    title: '',
    description: '',
};

const DataModelSchema = Yup.object().shape({
    title: Yup.string().required('Please enter a title'),
    description: Yup.string(),
});
