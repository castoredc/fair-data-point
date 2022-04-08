import React, { Component } from "react";
import FormItem from "components/Form/FormItem";
import { Button, Modal, Stack } from "@castoredc/matter";
import { PrefixType } from "types/PrefixType";
import { NodesType } from "types/NodesType";
import { ModuleType } from "types/ModuleType";
import { Field, Form, Formik } from "formik";
import Select from "components/Input/Formik/Select";
import UriInput from "components/Input/Formik/UriInput";
import * as Yup from "yup";
import { toast } from "react-toastify";
import ToastContent from "components/ToastContent";
import { apiClient } from "../network";

type TripleModalProps = {
  show: boolean;
  handleClose: () => void;
  data: any;
  nodes: NodesType;
  prefixes: PrefixType[];
  module: ModuleType;
  onSaved: () => void;
  modelId: string;
  versionId: string;
};

type TripleModalState = {
  initialValues: any;
  validation: any;
};

export default class TripleModal extends Component<
  TripleModalProps,
  TripleModalState
> {
  constructor(props) {
    super(props);

    this.state = {
      initialValues: props.data ? props.data : defaultData,
      validation: {},
    };
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
    const { show, data } = this.props;

    if (show !== prevProps.show || data !== prevProps.data) {
      this.setState({
        initialValues: data ? data : defaultData,
      });
    }
  }

  handleSubmit = (values, { setSubmitting }) => {
    const { modelId, versionId, module, onSaved } = this.props;

    apiClient
      .post(
        "/api/model/" +
          modelId +
          "/v/" +
          versionId +
          "/module/" +
          module.id +
          "/triple" +
          (values.id ? "/" + values.id : ""),
        values
      )
      .then((response) => {
        setSubmitting(false);

        onSaved();
      })
      .catch((error) => {
        setSubmitting(false);

        if (error.response && error.response.status === 400) {
          this.setState({
            validation: error.response.values.fields,
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

  getOptions = (type) => {
    const { nodes } = this.props;

    return nodes[type].map((node) => {
      return { value: node.id, label: node.title, repeated: node.repeated };
    });
  };

  render() {
    const { show, handleClose, module, prefixes } = this.props;
    const { initialValues, validation } = this.state;

    const edit = !!initialValues.id;
    const title = edit ? "Edit triple" : "Add triple";

    return (
      <Modal
        open={show}
        onClose={handleClose}
        title={title}
        accessibleName={title}
      >
        <Formik
          initialValues={initialValues}
          validationSchema={TripleSchema}
          onSubmit={this.handleSubmit}
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
            const subjectSelectable =
              values.subjectType === "internal" ||
              values.subjectType === "external";
            let subjectOptions = subjectSelectable
              ? this.getOptions(values.subjectType)
              : [];
            const objectSelectable =
              values.objectType === "internal" ||
              values.objectType === "external" ||
              values.objectType === "value" ||
              values.objectType === "literal";
            let objectOptions = objectSelectable
              ? this.getOptions(values.objectType)
              : [];

            if (module && values.objectType === "value" && module.repeated) {
              objectOptions = objectOptions.filter((option) => {
                return option.repeated;
              });
            }

            if (
              module &&
              values.objectType === "internal" &&
              !module.repeated
            ) {
              objectOptions = objectOptions.filter((option) => {
                return option.repeated === false;
              });
            }

            if (
              module &&
              values.subjectType === "internal" &&
              !module.repeated
            ) {
              subjectOptions = subjectOptions.filter((option) => {
                return option.repeated === false;
              });
            }

            return (
              <Form>
                <FormItem label="Subject">
                  <Stack>
                    <FormItem label="Type">
                      <Field
                        component={Select}
                        options={tripleTypes.subject}
                        serverError={validation}
                        name="subjectType"
                        width="tiny"
                        menuPosition="fixed"
                      />
                    </FormItem>

                    {subjectSelectable && (
                      <FormItem label="Node">
                        <Field
                          component={Select}
                          options={subjectOptions}
                          serverError={validation}
                          name="subjectValue"
                          width="small"
                          menuPosition="fixed"
                        />
                      </FormItem>
                    )}
                  </Stack>
                </FormItem>

                <FormItem label="Predicate">
                  <FormItem label="URI">
                    <Field
                      component={UriInput}
                      name="predicateValue"
                      serverError={validation}
                      width="100%"
                      inputSize="100%"
                      prefixes={prefixes}
                    />
                  </FormItem>
                </FormItem>

                <FormItem label="Object">
                  <Stack>
                    <FormItem label="Type">
                      <Field
                        component={Select}
                        options={tripleTypes.object}
                        serverError={validation}
                        name="objectType"
                        width="tiny"
                        menuPosition="fixed"
                      />
                    </FormItem>

                    {objectSelectable && (
                      <FormItem label="Node">
                        <Field
                          component={Select}
                          options={objectOptions}
                          serverError={validation}
                          name="objectValue"
                          width="small"
                          menuPosition="fixed"
                        />
                      </FormItem>
                    )}
                  </Stack>
                </FormItem>

                <Button type="submit" disabled={isSubmitting}>
                  {values.id ? "Edit triple" : "Add triple"}
                </Button>
              </Form>
            );
          }}
        </Formik>
      </Modal>
    );
  }
}

export const tripleTypes = {
  subject: [
    { value: "internal", label: "Internal" },
    { value: "external", label: "External" },
    { value: "record", label: "Record" },
  ],
  object: [
    { value: "internal", label: "Internal" },
    { value: "external", label: "External" },
    { value: "record", label: "Record" },
    { value: "literal", label: "Literal" },
    { value: "value", label: "Value" },
  ],
};

const defaultData = {
  subjectType: "internal",
  subjectValue: "",
  predicateValue: "",
  objectType: "internal",
  objectValue: "",
};

const TripleSchema = Yup.object().shape({
  subjectType: Yup.string().oneOf(
    ["internal", "external", "record"],
    "Please select a subject type"
  ),
  subjectValue: Yup.string().when("subjectType", {
    is: (subjectType) =>
      subjectType === "internal" || subjectType === "external",
    then: Yup.string().required("Please select a node"),
  }),
  predicateValue: Yup.string()
    .required("Please enter a predicate")
    .url("Please enter a valid predicate"),
  objectType: Yup.string().oneOf(
    ["internal", "external", "record", "literal", "value"],
    "Please select an object type"
  ),
  objectValue: Yup.string().when("objectType", {
    is: (objectType) =>
      objectType === "internal" ||
      objectType === "external" ||
      objectType === "value" ||
      objectType === "literal",
    then: Yup.string().required("Please select a node"),
  }),
});
