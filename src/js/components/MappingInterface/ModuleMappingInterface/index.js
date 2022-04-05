import React, { Component } from "react";
import { ValidatorForm } from "react-form-validator-core";
import RadioGroup from "../../Input/RadioGroup";
import FormItem from "../../Form/FormItem";
import Dropdown from "../../Input/Dropdown";
import { Button, Heading } from "@castoredc/matter";
import { toast } from "react-toastify";
import ToastContent from "../../ToastContent";
import { apiClient } from "src/js/network";

export default class ModuleMappingInterface extends Component {
  constructor(props) {
    super(props);

    this.state = {
      data: {
        type:
          props.mapping && props.mapping.element
            ? props.mapping.element.structureType
            : "report",
        element:
          props.mapping && props.mapping.element
            ? props.mapping.element.id
            : null,
      },
    };
  }

  componentDidUpdate(prevProps) {
    const { mapping } = this.props;

    if (mapping !== prevProps.mapping) {
      const newData = mapping
        ? {
            type: mapping.element.structureType,
            element: mapping.element.id,
          }
        : null;

      this.state = {
        data: newData,
      };
    }
  }

  handleTypeChange = (event) => {
    this.setState({
      data: {
        type: event.target.value,
        element: "",
      },
    });
  };

  handleChange = (event) => {
    const { data } = this.state;

    this.setState({
      data: {
        ...data,
        [event.target.name]: event.target.value,
      },
    });
  };

  handleSubmit = () => {
    const { mapping, dataset, distribution, versionId, onSave } = this.props;
    const { data } = this.state;

    if (!this.form.isFormValid()) {
      return;
    }

    this.setState({ isLoading: true });

    apiClient
      .post(
        "/api/dataset/" +
          dataset +
          "/distribution/" +
          distribution.slug +
          "/contents/rdf/v/" +
          versionId +
          "/module",
        {
          type: "module",
          module: mapping.module.id,
          structureType: data.type,
          element: data.element,
        }
      )
      .then(() => {
        this.setState(
          {
            isLoading: false,
          },
          () => {
            toast.success(
              <ToastContent
                type="success"
                message="The mapping was successfully saved."
              />,
              {
                position: "top-right",
              }
            );

            onSave();
          }
        );
      })
      .catch((error) => {
        this.setState({
          isLoading: false,
        });

        const message =
          error.response && typeof error.response.data.error !== "undefined"
            ? error.response.data.error
            : "An error occurred while saving the mapping";
        toast.error(<ToastContent type="error" message={message} />);
      });
  };

  render() {
    const { structure, mapping } = this.props;
    const { data, isLoading } = this.state;

    const structureItems =
      data.type !== "" && data.type !== null ? structure[data.type] : [];

    const options = structureItems.map((item) => {
      return { label: item.name, value: item.id };
    });

    const required = "This field is required";

    return (
      <>
        <Heading type="Panel">
          {`${mapping.element ? `Edit` : `Add`} mapping for ${
            mapping.module.displayName
          }`}
        </Heading>

        <ValidatorForm
          ref={(node) => (this.form = node)}
          onSubmit={this.handleSubmit}
          method="post"
        >
          <FormItem label="Type">
            <RadioGroup
              validators={["required"]}
              errorMessages={[required]}
              options={[
                { value: "report", label: "Report" },
                { value: "survey", label: "Survey" },
              ]}
              onChange={this.handleTypeChange}
              value={data.type}
              name="type"
              variant="horizontal"
            />
          </FormItem>

          <FormItem label="Element">
            <Dropdown
              validators={["required"]}
              errorMessages={[required]}
              options={options}
              name="element"
              onChange={(e) => {
                this.handleChange({
                  target: { name: "element", value: e.value },
                });
              }}
              value={options.filter(({ value }) => value === data.element)}
              menuPosition="fixed"
            />
          </FormItem>

          <Button type="submit" disabled={isLoading}>
            {mapping && mapping.element ? "Edit mapping" : "Add mapping"}
          </Button>
        </ValidatorForm>
      </>
    );
  }
}
