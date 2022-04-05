import React, { Component } from "react";
import "./StudyStructure.scss";
import { toast } from "react-toastify";
import ToastContent from "../ToastContent";
import FieldListItem from "../ListItem/FieldListItem";
import StudyStructureNavigator from "./StudyStructureNavigator";
import { LoadingOverlay, Tabs } from "@castoredc/matter";
import { apiClient } from "src/js/network";

export default class StudyStructure extends Component {
  constructor(props) {
    super(props);
    this.state = {
      isLoadingStructure: true,
      hasLoadedStructure: false,
      structure: null,

      isLoadingFields: true,

      fields: [],
      selectedStep: null,
      selectedType: "study",

      selectableTypes: props.types || ["study", "report", "survey"],
    };
  }

  componentDidMount() {
    this.getStructure();
  }

  componentDidUpdate(prevProps, prevState, snapshot) {
    const { types } = this.props;

    if (types.length !== prevProps.types.length) {
      const selectableTypes = this.parseTypes();
      const selectedType = selectableTypes[0];

      this.setState({ selectableTypes, selectedType });
    }
  }

  getStructure = () => {
    const { studyId } = this.props;
    const { selectedType } = this.state;

    this.setState({
      isLoading: true,
    });

    apiClient
      .get("/api/castor/study/" + studyId + "/structure")
      .then((response) => {
        this.setState(
          {
            structure: response.data,
            isLoadingStructure: false,
          },
          () => {
            this.parseTypes();
            this.handleStepSwitch(response.data[selectedType][0].steps[0]);
          }
        );
      })
      .catch((error) => {
        if (
          error.response &&
          typeof error.response.data.error !== "undefined"
        ) {
          toast.error(
            <ToastContent type="error" message={error.response.data.error} />
          );
        } else {
          toast.error(
            <ToastContent type="error" message="An error occurred" />
          );
        }

        this.setState({
          isLoading: false,
        });
      });
  };

  parseTypes = () => {
    const { types } = this.props;
    const { structure } = this.state;

    const selection = types || ["study", "report", "survey"];

    if (!structure) {
      return selection;
    }

    return [
      ...(selection.includes("study") && structure.study.length > 0
        ? ["study"]
        : []),
      ...(selection.includes("report") && structure.report.length > 0
        ? ["report"]
        : []),
      ...(selection.includes("survey") && structure.survey.length > 0
        ? ["survey"]
        : []),
    ];
  };

  changeType = (tabIndex) => {
    const { structure } = this.state;

    this.setState(
      {
        selectedType: tabIndex,
        fields: [],
      },
      () => {
        this.handleStepSwitch(structure[tabIndex][0].steps[0]);
      }
    );
  };

  handleStepSwitch = (step) => {
    const { studyId } = this.props;
    const { selectedStep } = this.state;

    this.setState({
      isLoadingFields: true,
      selectedStep: step,
    });

    apiClient
      .get(
        "/api/castor/study/" +
          studyId +
          "/structure/step/" +
          step.id +
          "/fields"
      )
      .then((response) => {
        this.setState({
          fields: response.data,
          isLoadingFields: false,
        });
      })
      .catch((error) => {
        if (
          error.response &&
          typeof error.response.data.error !== "undefined"
        ) {
          toast.error(
            <ToastContent type="error" message={error.response.data.error} />
          );
        } else {
          toast.error(
            <ToastContent type="error" message="An error occurred" />
          );
        }

        this.setState({
          isLoadingFields: false,
          selectedStep: selectedStep,
        });
      });
  };

  render() {
    const {
      selectable,
      selection,
      onSelect,
      dataFormat,
      dataType,
      dataTransformation,
    } = this.props;
    const {
      structure,
      isLoadingFields,
      fields,
      selectedStep,
      selectedType,
      isLoadingStructure,
      selectableTypes,
    } = this.state;

    if (isLoadingStructure) {
      return <LoadingOverlay accessibleLabel="Loading structure" />;
    }

    const cannotBeSelected = (
      <div className="StudyStructureType">
        <div className="NoResults">This type cannot be selected.</div>
      </div>
    );

    const tabContent = (
      <div className="StudyStructureType">
        <StudyStructureNavigator
          contents={structure[selectedType]}
          selectedStep={selectedStep}
          handleStepSwitch={this.handleStepSwitch}
        />

        <div className="StudyStructureContents">
          <div className="Fields">
            {isLoadingFields ? (
              <LoadingOverlay accessibleLabel="Loading fields" />
            ) : (
              fields.map((field) => {
                const selected =
                  selection.filter((id) => id === field.id).length > 0;

                return (
                  <FieldListItem
                    selectable={selectable}
                    selected={selected}
                    onSelect={onSelect}
                    key={field.id}
                    id={field.id}
                    type={field.type}
                    label={field.label}
                    stepNumber={selectedStep.position}
                    number={field.number}
                    variableName={field.variableName}
                    exportable={field.exportable}
                    dataFormat={dataFormat}
                    dataType={dataType}
                    dataTransformation={dataTransformation}
                  />
                );
              })
            )}
          </div>
        </div>
      </div>
    );

    return (
      <div className="StudyStructure PageTabs">
        <Tabs
          onChange={this.changeType}
          selected={selectedType}
          tabs={{
            study: {
              title: "Study",
              content: selectableTypes.includes("study")
                ? tabContent
                : cannotBeSelected,
            },
            report: {
              title: "Reports",
              content: selectableTypes.includes("report")
                ? tabContent
                : cannotBeSelected,
            },
            survey: {
              title: "Surveys",
              content: selectableTypes.includes("survey")
                ? tabContent
                : cannotBeSelected,
            },
          }}
        />
      </div>
    );
  }
}
