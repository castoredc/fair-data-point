import React, { Component } from "react";

import "../Form.scss";
import { toast } from "react-toastify";
import ToastContent from "../../ToastContent";
import FormItem from "./../FormItem";
import { mergeData } from "../../../util";
import { Button, LoadingOverlay, Stack } from "@castoredc/matter";
import FormHeading from "../FormHeading";
import { Field, Form, Formik } from "formik";
import Choice from "components/Input/Formik/Choice";
import Select from "components/Input/Formik/Select";
import Input from "components/Input/Formik/Input";
import * as Yup from "yup";
import SingleChoice from "components/Input/Formik/SingleChoice";
import * as H from "history";
import { apiClient } from "src/js/network";

interface DistributionFormProps {
  distribution?: any;
  dataset?: any;
  history: H.History;
  mainUrl: string;
}

interface DistributionFormState {
  initialValues: any;
  languages: any;
  licenses: any;
  dataModels: any;
  distribution: any;
  hasLoadedLanguages: any;
  hasLoadedLicenses: any;
  hasLoadedDataModels: any;
  update: boolean;
  validation?: any;
}

export default class DistributionForm extends Component<
  DistributionFormProps,
  DistributionFormState
> {
  constructor(props) {
    super(props);

    let data = props.distribution
      ? mergeData(defaultData, props.distribution)
      : defaultData;
    const apiUser = props.distribution ? props.distribution.hasApiUser : false;
    data = {
      ...data,
      useApiUser: apiUser,
      apiUserEncrypted: apiUser,
    };

    if (typeof data.dataModel === "object" && data.dataModel !== "") {
      data = {
        ...data,
        dataModel: data.dataModel.dataModel,
        dataModelVersion: data.dataModel.id,
      };
    }

    this.state = {
      initialValues: data,
      languages: [],
      licenses: [],
      dataModels: [],
      hasLoadedLanguages: false,
      hasLoadedLicenses: false,
      hasLoadedDataModels: false,
      distribution: props.distribution ? props.distribution : null,
      update: !!props.distribution,
    };
  }

  componentDidMount() {
    this.getLanguages();
    this.getLicenses();
    this.getDataModels();
  }

  getLanguages = () => {
    apiClient
      .get("/api/languages")
      .then((response) => {
        this.setState({
          languages: response.data,
          hasLoadedLanguages: true,
        });
      })
      .catch((error) => {
        toast.error(<ToastContent type="error" message="An error occurred" />);
      });
  };

  getLicenses = () => {
    apiClient
      .get("/api/licenses")
      .then((response) => {
        this.setState({
          licenses: response.data,
          hasLoadedLicenses: true,
        });
      })
      .catch(() => {
        toast.error(<ToastContent type="error" message="An error occurred" />);
      });
  };

  getDataModels = () => {
    apiClient
      .get("/api/model/my")
      .then((response) => {
        this.setState({
          dataModels: response.data.map((dataModel) => {
            const versions = dataModel.versions.map((version) => {
              return { value: version.id, label: version.version };
            });

            return {
              label: dataModel.title,
              value: dataModel.id,
              versions: versions,
            };
          }),
          hasLoadedDataModels: true,
        });
      })
      .catch(() => {
        toast.error(<ToastContent type="error" message="An error occurred" />);
      });
  };

  handleSubmit = (values, { setSubmitting }) => {
    const { dataset, distribution, mainUrl, history } = this.props;

    const url =
      "/api/dataset/" +
      dataset +
      "/distribution" +
      (distribution ? "/" + distribution.slug : "");

    apiClient
      .post(url, values)
      .then((response) => {
        this.setState({
          distribution: response.data,
        });

        if (distribution) {
          toast.success(
            <ToastContent
              type="success"
              message="The distribution details are saved successfully"
            />,
            {
              position: "top-right",
            }
          );
        } else {
          history.push(
            mainUrl + "/distributions/" + response.data.slug + "/metadata"
          );
        }

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

  render() {
    const {
      initialValues,
      licenses,
      dataModels,
      distribution,
      hasLoadedLanguages,
      hasLoadedLicenses,
      hasLoadedDataModels,
    } = this.state;

    if (!hasLoadedLanguages || !hasLoadedLicenses || !hasLoadedDataModels) {
      return <LoadingOverlay accessibleLabel="Loading distribution" />;
    }

    return (
      <Formik initialValues={initialValues} onSubmit={this.handleSubmit}>
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
          const currentDataModel = dataModels.find(
            ({ value }) => value === values.dataModel
          );

          return (
            <Form>
              <div className="FormContent">
                <FormItem label="Type">
                  <Field
                    component={Choice}
                    options={distributionTypes}
                    collapse
                    name="type"
                  />
                </FormItem>

                {values.type === "csv" && (
                  <FormItem>
                    <Field
                      component={SingleChoice}
                      labelText="Include all data"
                      name="includeAllData"
                    />
                  </FormItem>
                )}

                {values.type === "rdf" && (
                  <>
                    <Stack>
                      <FormItem label="Data model">
                        <Field
                          component={Select}
                          options={dataModels}
                          name="dataModel"
                        />
                      </FormItem>
                      {currentDataModel && (
                        <FormItem label="Data model version">
                          <Field
                            component={Select}
                            options={currentDataModel.versions}
                            name="dataModelVersion"
                            // value={currentDataModel.versions.filter(({value}) => value === data.dataModelVersion)}
                          />
                        </FormItem>
                      )}
                    </Stack>
                  </>
                )}

                {values.type !== "" && (
                  <>
                    <FormItem label="Slug">
                      <Field component={Input} name="slug" />
                    </FormItem>

                    <FormItem label="License">
                      <Field
                        component={Select}
                        options={licenses}
                        name="license"
                        menuPosition="fixed"
                        menuPlacement="auto"
                      />
                    </FormItem>

                    <FormItem label="Access type">
                      <Field
                        component={Choice}
                        options={accessTypes}
                        name="accessRights"
                        collapse
                      />
                    </FormItem>

                    {distribution && (
                      <FormItem label="Publish distribution">
                        <Field
                          component={Choice}
                          options={[
                            {
                              labelText: "Yes",
                              value: true,
                            },
                            {
                              labelText: "No",
                              value: false,
                            },
                          ]}
                          collapse
                          name="published"
                        />
                      </FormItem>
                    )}

                    <FormHeading label="Castor EDC API Credentials" />

                    <FormItem>
                      <Field
                        component={SingleChoice}
                        labelText="Use API User"
                        name="useApiUser"
                      />
                    </FormItem>

                    {values.useApiUser && (
                      <div>
                        {values.apiUserEncrypted ? (
                          <div>
                            <p>
                              The API Credentials for this distribution are
                              encrypted.
                            </p>

                            <Button
                              buttonType="danger"
                              onClick={() =>
                                setFieldValue("apiUserEncrypted", false)
                              }
                            >
                              Change API Credentials
                            </Button>
                          </div>
                        ) : (
                          <div>
                            <FormItem label="Email address">
                              <Field component={Input} name="apiUser" />
                            </FormItem>

                            <Stack>
                              <FormItem label="Client ID">
                                <Field component={Input} name="clientId" />
                              </FormItem>

                              <FormItem label="Client Secret">
                                <Field component={Input} name="clientSecret" />
                              </FormItem>
                            </Stack>
                          </div>
                        )}
                      </div>
                    )}
                  </>
                )}
              </div>

              <div className="FormButtons">
                <Stack distribution="trailing">
                  {distribution ? (
                    <Button disabled={isSubmitting} type="submit">
                      Update distribution
                    </Button>
                  ) : (
                    <Button disabled={isSubmitting} type="submit">
                      Add distribution
                    </Button>
                  )}
                </Stack>
              </div>
            </Form>
          );
        }}
      </Formik>
    );
  }
}

export const accessTypes = [
  { value: 1, labelText: "Public" },
  { value: 2, labelText: "Study Users" },
];

export const distributionTypes = [
  { value: "csv", labelText: "CSV Distribution" },
  { value: "rdf", labelText: "RDF Distribution" },
];

export const defaultData = {
  type: "",
  slug: "",
  accessRights: null,
  includeAllData: false,
  dataModel: "",
  dataModelVersion: "",
  license: null,
  useApiUser: false,
  apiUserEncrypted: false,
  apiUser: "",
  clientId: "",
  clientSecret: "",
  published: false,
};

const DistributionSchema = Yup.object().shape({
  type: Yup.string()
    .oneOf(["csv", "rdf"])
    .required("Please select a distribution type"),
  slug: Yup.string().required("Please select a slug"),
  accessRights: Yup.number()
    .oneOf(accessTypes.map((type) => type.value))
    .required("Please select an access type"),
  includeAllData: Yup.boolean()
    .nullable()
    .when("type", {
      is: "csv",
      then: Yup.boolean().required(
        "Please select if all data should be included"
      ),
    }),
  dataModel: Yup.string().when("type", {
    is: "rdf",
    then: Yup.string().required("Please select a data model"),
  }),
  dataModelVersion: Yup.string().when("type", {
    is: "rdf",
    then: Yup.string().required("Please select a data model version"),
  }),
  license: Yup.string().required("Please select a license"),
  useApiUser: Yup.boolean().required(),
  apiUser: Yup.string().when("useApiUser", {
    is: true,
    then: Yup.string().required("Please enter an email address"),
  }),
  clientId: Yup.string().when("useApiUser", {
    is: true,
    then: Yup.string().required("Please enter a client ID"),
  }),
  clientSecret: Yup.string().when("useApiUser", {
    is: true,
    then: Yup.string().required("Please enter a client secret"),
  }),
  published: Yup.boolean().required(
    "Please select if this distribution should be published"
  ),
});
