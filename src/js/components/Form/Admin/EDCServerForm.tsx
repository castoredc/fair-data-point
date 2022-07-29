import React, {Component} from "react";

import "../Form.scss";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import FormItem from "./../FormItem";
import {mergeData} from "../../../util";
import {Button, Stack} from "@castoredc/matter";
import {Field, Form, Formik} from "formik";
import Input from "components/Input/Formik/Input";
import SingleChoice from "components/Input/Formik/SingleChoice";
import * as Yup from "yup";
import {apiClient} from "src/js/network";
import {ServerType} from "types/ServerType";
import {FormikHelpers} from "formik/dist/types";

interface EDCServerFormProps {
    edcServer?: ServerType;
    handleSubmit: (values: ServerType) => void,
}

interface EDCServerFormState {
    initialValues: any;
    update: boolean;
    validation?: any;
}

export default class EDCServerForm extends Component<EDCServerFormProps, EDCServerFormState> {
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

    handleSubmit = (values, {setSubmitting}) => {
        const {edcServer, handleSubmit} = this.props;
        setSubmitting(true);

        apiClient
            .post("/api/castor/servers" + (edcServer ? "/" + edcServer.id : ""), values)
            .then((response) => {
                setSubmitting(false);

                // New server:
                if (!edcServer && response.data.id) {
                    const message = `The EDC Server ${response.data.name} was saved successfully with id ${response.data.id}`
                    toast.success(
                        <ToastContent
                            type="success"
                            message={message}
                        />,
                        {
                            position: "top-right",
                        }
                    );

                    handleSubmit(response.data);

                    return;
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
                        <ToastContent type="error" message="An error occurred"/>,
                        {
                            position: "top-center",
                        }
                    );
                }
            });
    };

    render() {
        const {initialValues, validation} = this.state;
        const {edcServer} = this.props;

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
                                        name="clientId"
                                        serverError={validation}
                                    />
                                </FormItem>
                                <FormItem label="Client secret">
                                    <Field
                                        component={Input}
                                        name="clientSecret"
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
    clientId: "",
    clientSecret: "",
};

const EDCServerSchema = Yup.object().shape({
    name: Yup.string().required("Please enter a name"),
    url: Yup.string().required("Please enter the server URL"),
    flag: Yup.string().required("Please enter the two-letter flag identifier"),
    default: Yup.boolean().required(
        "Please enter if this is the new default Server"
    ),
    clientId: Yup.string().required("Please enter the client ID"),
    clientSecret: Yup.string().required("Please enter the client secret"),
});
