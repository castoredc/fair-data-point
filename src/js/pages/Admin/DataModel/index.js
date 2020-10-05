import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import DataModelPrefixes from "./DataModelPrefixes";
import DataModelModules from "./DataModelModules";
import DataModelDetails from "./DataModelDetails";
import DataModelNodes from "./DataModelNodes";
import {ViewHeader} from "@castoredc/matter";
import DataModelPreview from "./DataModelPreview";
import DataModelVersions from "./DataModelVersions";
import DataModelImportExport from "./DataModelImportExport";
import DocumentTitle from "../../../components/DocumentTitle";
import SideBar from "../../../components/SideBar";

export default class DataModel extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingDataModel: true,
            hasLoadedDataModel: false,
            dataModel:          null,
            showModal:          false,
            currentVersion:     null,
        };
    }

    componentDidMount() {
        this.getDataModel();
    }

    componentDidUpdate(prevProps) {
        const {match} = this.props;

        if (match.params.version !== prevProps.match.params.version) {
            this.getDataModel();
        }
    }

    getDataModel = () => {
        const {match} = this.props;

        this.setState({
            isLoadingDataModel: true,
        });

        axios.get('/api/model/' + match.params.model)
            .then((response) => {
                const versions = response.data.versions.map((version) => {
                    return {value: version.id, label: version.version};
                });

                const currentVersion = (match.params.version && match.params.version !== 'versions') ? match.params.version : versions.slice(-1)[0].label;

                this.setState({
                    dataModel:          response.data,
                    isLoadingDataModel: false,
                    hasLoadedDataModel: true,
                    versions:           versions,
                    currentVersion:     versions.find(({label}) => label === currentVersion),
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

    openModal = () => {
        this.setState({
            showModal: true,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    handleVersionChange = (version) => {
        const {currentVersion} = this.state;
        const newVersion = version.label;

        const newUrl = window.location.pathname.replace('/' + currentVersion.label + '/', '/' + newVersion + '/');

        if (window.location.pathname !== newUrl) {
            this.props.history.push(newUrl);
        } else {
            this.setState({
                currentVersion: version
            });
        }
    };

    render() {
        const {location} = this.props;
        const {dataModel, isLoadingDataModel, versions, currentVersion} = this.state;

        if (!dataModel && isLoadingDataModel) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <DocumentTitle title={`FDP Admin | Data Model | ${dataModel.title}`}/>

            <SideBar
                location={location}
                onVersionChange={this.handleVersionChange}
                items={[
                    {
                        to: '/admin/model/' + dataModel.id,
                        exact: true,
                        title: 'Data model',
                        icon: 'structure'
                    },
                    {
                        to: '/admin/model/' + dataModel.id + '/versions',
                        exact: true,
                        title: 'Versions',
                        customIcon: 'versions'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        type: 'version',
                        current: currentVersion,
                        versions: versions
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/admin/model/' + dataModel.id + '/' + currentVersion.label + '/modules',
                        exact: true,
                        title: 'Groups',
                        customIcon: 'modules'
                    },
                    {
                        to: '/admin/model/' + dataModel.id + '/' + currentVersion.label + '/nodes',
                        exact: true,
                        title: 'Nodes',
                        customIcon: 'node'
                    },
                    {
                        to: '/admin/model/' + dataModel.id + '/' + currentVersion.label + '/prefixes',
                        exact: true,
                        title: 'Prefixes',
                        customIcon: 'prefix'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/admin/model/' + dataModel.id + '/' + currentVersion.label + '/import-export',
                        exact: true,
                        title: 'Import/export',
                        icon: 'upload'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/admin/model/' + dataModel.id + '/' + currentVersion.label + '/preview',
                        exact: true,
                        title: 'Preview',
                        customIcon: 'preview'
                    }
                ]}
            />

            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>{dataModel.title}</ViewHeader>
                </div>

                {isLoadingDataModel ? <InlineLoader/> : <Switch>
                    <Route path="/admin/model/:model" exact
                           render={(props) => <DataModelDetails {...props} dataModel={dataModel}
                                                                version={currentVersion.value}/>}/>
                    <Route path="/admin/model/:model/versions" exact
                           render={(props) => <DataModelVersions {...props} getDataModel={this.getDataModel}
                                                                 dataModel={dataModel}
                                                                 version={currentVersion.value}/>}/>
                    <Route path="/admin/model/:model/:version/modules" exact
                           render={(props) => <DataModelModules {...props} dataModel={dataModel}
                                                                version={currentVersion.value}/>}/>
                    <Route path="/admin/model/:model/:version/nodes" exact
                           render={(props) => <DataModelNodes {...props} dataModel={dataModel}
                                                              version={currentVersion.value}/>}/>
                    <Route path="/admin/model/:model/:version/prefixes" exact
                           render={(props) => <DataModelPrefixes {...props} dataModel={dataModel}
                                                                 version={currentVersion.value}/>}/>
                    <Route path="/admin/model/:model/:version/preview" exact
                           render={(props) => <DataModelPreview {...props} dataModel={dataModel}
                                                                version={currentVersion.value}/>}/>
                    <Route path="/admin/model/:model/:version/import-export" exact
                           render={(props) => <DataModelImportExport {...props} dataModel={dataModel}
                                                                     getDataModel={this.getDataModel}
                                                                     version={currentVersion.value}/>}/>

                    <Route component={NotFound}/>
                </Switch>}
            </div>
        </div>;
    }
}