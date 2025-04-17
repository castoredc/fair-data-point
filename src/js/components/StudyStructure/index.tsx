import React, { Component } from 'react';
import './StudyStructure.scss';
import LoadingOverlay from 'components/LoadingOverlay';
import FieldListItem from '../ListItem/FieldListItem';
import StudyStructureNavigator from './StudyStructureNavigator';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import PageTabs from 'components/PageTabs';
import NoResults from 'components/NoResults';

interface StudyStructureProps extends ComponentWithNotifications {
    studyId: string;
    types?: string[];
    selectable?: boolean;
    selection: string[];
    onSelect: (fieldId: string, variableName: string, label: string) => void;
    dataFormat?: string;
    dataType?: string;
    dataTransformation?: boolean;
}

interface StudyStructureState {
    isLoadingStructure: boolean;
    hasLoadedStructure: boolean;
    structure: any;
    isLoadingFields: boolean;
    fields: any[];
    selectedStep: any;
    selectedType: string;
    selectableTypes: string[];
}

class StudyStructure extends Component<StudyStructureProps, StudyStructureState> {
    constructor(props: StudyStructureProps) {
        super(props);
        this.state = {
            isLoadingStructure: true,
            hasLoadedStructure: false,
            structure: null,
            isLoadingFields: true,
            fields: [],
            selectedStep: null,
            selectedType: 'study',
            selectableTypes: props.types || ['study', 'report', 'survey'],
        };
    }

    componentDidMount() {
        this.getStructure();
    }

    componentDidUpdate(prevProps: StudyStructureProps) {
        const { types } = this.props;

        if (types && types.length !== prevProps.types?.length) {
            const selectableTypes = this.parseTypes();
            const selectedType = selectableTypes[0];

            this.setState({ selectableTypes, selectedType });
        }
    }

    getStructure = () => {
        const { studyId, notifications } = this.props;
        const { selectedType } = this.state;

        this.setState({ isLoadingStructure: true });

        apiClient
            .get(`/api/castor/study/${studyId}/structure`)
            .then(response => {
                this.setState(
                    {
                        structure: response.data,
                        isLoadingStructure: false,
                    },
                    () => {
                        this.parseTypes();
                        this.handleStepSwitch(response.data[selectedType][0].steps[0]);
                    },
                );
            })
            .catch(error => {
                const errorMessage = error.response?.data?.error || 'An error occurred';
                notifications.show(errorMessage, { variant: 'error' });

                this.setState({ isLoadingStructure: false });
            });
    };

    parseTypes = () => {
        const { types } = this.props;
        const { structure } = this.state;

        const selection = types || ['study', 'report', 'survey'];

        if (!structure) {
            return selection;
        }

        return [
            ...(selection.includes('study') && structure.study.length > 0 ? ['study'] : []),
            ...(selection.includes('report') && structure.report.length > 0 ? ['report'] : []),
            ...(selection.includes('survey') && structure.survey.length > 0 ? ['survey'] : []),
        ];
    };

    changeType = (tabIndex: string) => {
        const { structure } = this.state;

        this.setState({ selectedType: tabIndex, fields: [] }, () => {
            this.handleStepSwitch(structure[tabIndex][0].steps[0]);
        });
    };

    handleStepSwitch = (step: any) => {
        const { studyId, notifications } = this.props;

        this.setState({
            isLoadingFields: true,
            selectedStep: step,
        });

        apiClient
            .get(`/api/castor/study/${studyId}/structure/step/${step.id}/fields`)
            .then(response => {
                this.setState({
                    fields: response.data,
                    isLoadingFields: false,
                });
            })
            .catch(error => {
                const errorMessage = error.response?.data?.error || 'An error occurred';
                notifications.show(errorMessage, { variant: 'error' });

                this.setState({
                    isLoadingFields: false,
                });
            });
    };

    render() {
        const { selectable, selection, onSelect, dataFormat, dataType, dataTransformation } = this.props;
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
                <NoResults>This type cannot be selected.</NoResults>
            </div>
        );

        const tabContent = (
            <div className="StudyStructureType">
                <StudyStructureNavigator contents={structure[selectedType]} selectedStep={selectedStep}
                                         handleStepSwitch={this.handleStepSwitch} />
                <div className="StudyStructureContents">
                    <div className="Fields">
                        {isLoadingFields ? (
                            <LoadingOverlay accessibleLabel="Loading fields" />
                        ) : (
                            fields.map((field: any) => {
                                const selected = selection.includes(field.id);
                                return (
                                    <FieldListItem
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
                <PageTabs
                    onChange={this.changeType}
                    selected={selectedType}
                    tabs={{
                        study: {
                            title: 'Study',
                            content: selectableTypes.includes('study') ? tabContent : cannotBeSelected,
                        },
                        report: {
                            title: 'Reports',
                            content: selectableTypes.includes('report') ? tabContent : cannotBeSelected,
                        },
                        survey: {
                            title: 'Surveys',
                            content: selectableTypes.includes('survey') ? tabContent : cannotBeSelected,
                        },
                    }}
                />
            </div>
        );
    }
}

export default withNotifications(StudyStructure);