import React, { Component } from "react";
import FormItem from "components/Form/FormItem";
import { toast } from "react-toastify";
import ToastContent from "components/ToastContent";
import { Button, Modal, Stack } from "@castoredc/matter";
import ConfirmModal from "modals/ConfirmModal";
import { classNames } from "../util";
import DependencyModal from "modals/DependencyModal";
import { Field, Form, Formik } from "formik";
import Input from "components/Input/Formik/Input";
import Select from "components/Input/Formik/Select";
import SingleChoice from "components/Input/Formik/SingleChoice";
import { NodeType } from "types/NodeType";
import { PrefixType } from "types/PrefixType";
import * as Yup from "yup";
import { apiClient } from "../network";

type DataModelModuleModalProps = {
  show: boolean;
  data: any;
  orderOptions: any;
  modelId: string;
  versionId: string;
  onSaved: () => void;
  handleClose: () => void;
  valueNodes: NodeType[];
  prefixes: PrefixType[];
};

type DataModelModuleModalState = {
  initialValues: any;
  validation: any;
  showDependencyModal: boolean;
};

export default class DataModelModuleModal extends Component<
  DataModelModuleModalProps,
  DataModelModuleModalState
> {
  constructor(props) {
    super(props);

    this.state = {
      initialValues: this.handleNewData(),
      validation: {},
      showDependencyModal: false,
    };
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
    const { show, data } = this.props;

    if (show !== prevProps.show || data !== prevProps.data) {
      this.setState({
        initialValues: this.handleNewData(),
      });
    }
  }

  handleNewData = () => {
    const { data, orderOptions } = this.props;

    let newData = defaultData;

    if (data !== null) {
      newData = data;
    } else {
      newData.order = orderOptions.slice(-1)[0].value;
    }

    return newData;
  };

  handleSubmit = (values, { setSubmitting }) => {
    const { modelId, versionId, onSaved } = this.props;

    apiClient
      .post(
        "/api/model/" +
          modelId +
          "/v/" +
          versionId +
          "/module" +
          (values.id ? "/" + values.id : ""),
        values
      )
      .then(() => {
        onSaved();
        setSubmitting(false);
      })
      .catch((error) => {
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
        setSubmitting(false);
      });
  };

  handleDelete = (id, callback) => {
    const { modelId, versionId, onSaved } = this.props;

    apiClient
      .delete("/api/model/" + modelId + "/v/" + versionId + "/module/" + id)
      .then(() => {
        callback();
        onSaved();
      })
      .catch((error) => {
        toast.error(<ToastContent type="error" message="An error occurred" />, {
          position: "top-center",
        });
      });
  };

  openDependencyModal = () => {
    this.setState({
      showDependencyModal: true,
    });
  };

  closeDependencyModal = () => {
    this.setState({
      showDependencyModal: false,
    });
  };

  render() {
    const { show, handleClose, orderOptions, valueNodes, prefixes } =
      this.props;
    const { initialValues, validation, showDependencyModal } = this.state;

    const title = initialValues.id ? "Edit group" : "Add group";

    return (
      <Modal
        open={show}
        onClose={handleClose}
        title={title}
        accessibleName={title}
      >
        <Formik
          initialValues={initialValues}
          onSubmit={this.handleSubmit}
          validationSchema={DataModelModuleSchema}
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
                <DependencyModal
                  show={showDependencyModal}
                  handleClose={this.closeDependencyModal}
                  save={(dependencies) => {
                    setFieldValue("dependencies", dependencies);
                    this.setState({
                      showDependencyModal: false,
                    });
                  }}
                  valueNodes={valueNodes}
                  prefixes={prefixes}
                  dependencies={values.dependencies}
                />

                <FormItem label="Title">
                  <Field
                    component={Input}
                    name="title"
                    serverError={validation}
                  />
                </FormItem>

                <FormItem label="Position">
                  <Field
                    component={Select}
                    options={orderOptions}
                    name="order"
                    serverError={validation}
                    menuPosition="fixed"
                  />
                </FormItem>

                <FormItem>
                  <Field
                    component={SingleChoice}
                    labelText="Repeated"
                    name="repeated"
                    serverError={validation}
                  />
                </FormItem>

                <FormItem label="Dependent">
                  <Field
                    component={SingleChoice}
                    labelText="Dependent"
                    name="dependent"
                    serverError={validation}
                  />
                </FormItem>

                {values.dependent && (
                  <FormItem>
                    <Button
                      buttonType="secondary"
                      onClick={this.openDependencyModal}
                      icon="decision"
                    >
                      Edit dependencies
                    </Button>
                  </FormItem>
                )}

                <div className={classNames(values.id && "HasConfirmButton")}>
                  <Stack alignment="normal" distribution="equalSpacing">
                    {values.id && (
                      <ConfirmModal
                        title="Delete group"
                        action="Delete group"
                        variant="danger"
                        onConfirm={(callback) =>
                          this.handleDelete(values.id, callback)
                        }
                        includeButton={true}
                      >
                        Are you sure you want to delete group{" "}
                        <strong>{values.title}</strong>?<br />
                        This will also delete all associated triples.
                      </ConfirmModal>
                    )}
                    <Button type="submit" disabled={isSubmitting}>
                      {values.id ? "Edit group" : "Add group"}
                    </Button>
                  </Stack>
                </div>
              </Form>
            );
          }}
        </Formik>
      </Modal>
    );
  }
}

const defaultData = {
  title: "",
  order: "",
  repeated: false,
  dependent: false,
  dependencies: {
    rules: [],
    combinator: "and",
    not: false,
  },
};

const DataModelModuleSchema = Yup.object().shape({
  title: Yup.string().required("Please enter a title"),
  order: Yup.string().required("Please select a position"),
  repeated: Yup.boolean(),
  dependent: Yup.boolean(),
});
