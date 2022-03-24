import React, {Component} from "react";
import DataModelMappingsDataTable from "components/DataTable/DataModelMappingsDataTable";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import {Dropdown as CastorDropdown, LoadingOverlay, Stack, Tabs} from "@castoredc/matter";
import FormItem from "components/Form/FormItem";
import MappingInterface from "components/MappingInterface";
import Split from "components/Layout/Split";
import PageBody from "components/Layout/Dashboard/PageBody";

export default class DistributionContentsRdf extends Component {
    constructor(props) {
        super(props);
        this.state = {
            selectedMapping: null,
            addedMapping: null,
            isLoadingDataModel: true,
            hasLoadedDataModel: false,
            currentVersion: null,
            dataModel: null,
            selectedType: 'node',
        };
    }

    componentDidMount() {
        this.getDataModel();
    }

    getDataModel = () => {
        const {distribution} = this.props;

        this.setState({
            isLoadingDataModel: true,
        });

        axios.get('/api/model/' + distribution.dataModel.dataModel)
            .then((response) => {
                this.setState({
                    dataModel: response.data,
                    isLoadingDataModel: false,
                    hasLoadedDataModel: true,
                    currentVersion: distribution.dataModel.id,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataModel: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data model';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    selectMapping = (mapping) => {
        this.setState({
            selectedMapping: mapping,
        });
    };

    onSave = () => {
        const {selectedMapping} = this.state;

        this.setState({
            addedMapping: selectedMapping,
            selectedMapping: null,
        });
    };

    handleVersionChange = (version) => {
        this.setState({
            currentVersion: version,
        });
    };

    changeTab = (tabIndex) => {
        this.setState({
            selectedType: tabIndex,
            selectedMapping: null,
        });
    };

    render() {
        const {
            selectedMapping,
            addedMapping,
            isLoadingDataModel,
            dataModel,
            currentVersion,
            selectedType
        } = this.state;
        const {distribution, dataset} = this.props;

        if (isLoadingDataModel) {
            return <LoadingOverlay accessibleLabel="Loading distribution"/>;
        }

        const versions = dataModel.versions.map((version) => {
            const label = (distribution.dataModel.id === version.id) ? version.version + ' (active)' : version.version;
            return {value: version.id, label: label};
        });

        return <PageBody>
            <div className="PageButtons" style={{flex: '0 0 60px'}}>
                <Stack distribution="trailing" alignment="end">
                    <FormItem label="Data model version" inline align="right">
                        <div className="Select">
                            <CastorDropdown
                                onChange={(e) => {
                                    this.handleVersionChange(e.value)
                                }}
                                value={versions.find(({value}) => value === currentVersion)}
                                options={versions}
                                menuPlacement="auto"
                                width="tiny"
                                menuPosition="fixed"
                            />
                        </div>
                    </FormItem>
                </Stack>
            </div>

            <div className="Mappings" style={{
                flex: 1,
                overflow: 'auto'
            }}>
                <Split sizes={[50, 50]}>
                    <div className="PageTabs">
                        <Tabs
                            onChange={this.changeTab}
                            selected={selectedType}
                            tabs={{
                                node: {
                                    title: 'Nodes',
                                    content: <DataModelMappingsDataTable
                                        dataset={dataset}
                                        distribution={distribution}
                                        onClick={this.selectMapping}
                                        lastHandledMapping={addedMapping}
                                        versionId={currentVersion}
                                        type="node"
                                    />,
                                },
                                module: {
                                    title: 'Groups',
                                    content: <DataModelMappingsDataTable
                                        dataset={dataset}
                                        distribution={distribution}
                                        onClick={this.selectMapping}
                                        lastHandledMapping={addedMapping}
                                        versionId={currentVersion}
                                        type="module"
                                    />,
                                },
                            }}
                        />
                    </div>

                    <MappingInterface
                        dataset={dataset}
                        distribution={distribution}
                        studyId={distribution.study.id}
                        mapping={selectedMapping}
                        onSave={this.onSave}
                        versionId={currentVersion}
                        type={selectedType}
                    />
                </Split>
            </div>
        </PageBody>;
    }
}