import React, { Component } from "react";

import "../Form.scss";
import { toast } from "react-toastify";
import ToastContent from "../../ToastContent";
import FormItem from "./../FormItem";
import { mergeData } from "../../../util";
import { Button, Stack } from "@castoredc/matter";
import { Field, Form, Formik } from "formik";
import Input from "components/Input/Formik/Input";
import SingleChoice from "components/Input/Formik/SingleChoice";
import * as Yup from "yup";
import { apiClient } from "src/js/network";
import {ServerType} from "types/ServerType";
import {FormikHelpers} from "formik/dist/types";

interface EDCServerFormProps {
    edcServer?: ServerType;
    handleSubmit: (values: any, formikHelpers: FormikHelpers<any>) => void,
}

interface EDCServerFormState {
    initialValues: any;
    update: boolean;
    validation?: any;
}

export default class EDCServerForm extends Component<
    EDCServerFormProps,
    EDCServerFormState
    > {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: props.edcServer
                ? mergeData(defaultData, props.edcServer)
                : defaultData,
            validation: {},
            update: !!props.edcServer,
        };
    }

    handleSubmit = (values, { setSubmitting }) => {
        const { edcServer } = this.props;

        apiClient
            .post("/api/castor/edc-server" + (edcServer ? "/" + edcServer.id : ""), values)
            .then((response) => {
                setSubmitting(false);

                if (edcServer) {
                    toast.success(
                        <ToastContent
                            type="success"
                            message="The EDC Server is saved successfully"
                        />,
                        {
                            position: "top-right",
                        }
                    );
                } else {
                    // No history available here, what to do on failure?
                    // history.push(
                    //     "/dashboard/edc-server/" + response.data.id
                    // );
                }
            })
            .catch((error) => {
                setSubmitting(false);

                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(
                        <ToastContent type="error" message="An error occurred" />,
                        {
                            position: "top-center",
                        }
                    );
                }
            });
    };

    render() {
        const { initialValues, validation } = this.state;
        const { edcServer } = this.props;

        return (
            <Formik
                initialValues={initialValues}
                onSubmit={this.handleSubmit}
                validationSchema={EDCServerSchema}
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
                      setFieldValue,
                  }) => {
                    return (
                        <Form>
                            <div className="FormContent">
                                <FormItem label="Name">
                                    <Field
                                        component={Input}
                                        name="name"
                                        serverError={validation}
                                    />
                                </FormItem>
                                <FormItem label="URL">
                                    <Field
                                        component={Input}
                                        name="url"
                                        serverError={validation}
                                    />
                                </FormItem>

                                <FormItem>
                                    <Field
                                        component={SingleChoice}
                                        labelText="Default server?"
                                        name="default"
                                    />
                                </FormItem>
                                <FormItem label="Client ID">
                                    <Field
                                        component={Input}
                                        name="client_id"
                                        serverError={validation}
                                    />
                                </FormItem>
                                <FormItem label="Client secret">
                                    <Field
                                        component={Input}
                                        name="client_secret"
                                        serverError={validation}
                                    />
                                </FormItem>
                            </div>

                            {edcServer ? (
                                <div className="FormButtons">
                                    <Stack distribution="trailing">
                                        <Button disabled={isSubmitting} type="submit">
                                            Update EDC server
                                        </Button>
                                    </Stack>
                                </div>
                            ) : (
                                <footer>
                                    <Button disabled={isSubmitting} type="submit">
                                        Add EDC server
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
    name: "",
    url: "https://",
    flag: "nl",
    default: false,
    client_id: "",
    client_secret: "",
};

const EDCServerSchema = Yup.object().shape({
    name: Yup.string().required("Please enter a name"),
    url: Yup.string().required("Please enter the server URL"),
    flag: Yup.string().required("Please enter the two-letter flag identifier"),
    default: Yup.boolean().required(
        "Please enter if this is the new default Server"
    ),
    client_id: Yup.string().required("Please enter the client ID"),
    client_secret: Yup.string().required("Please enter the client secret"),
});
