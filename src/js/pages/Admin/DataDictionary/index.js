import React, {Component} from "react";
import axios from "axios";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import {ViewHeader} from "@castoredc/matter";
import DocumentTitle from "../../../components/DocumentTitle";
import SideBar from "../../../components/SideBar";
import DataDictionaryDetails from "./DataDictionaryDetails";
import DataDictionaryVersions from "./DataDictionaryVersions";
import DataDictionaryGroups from "./DataDictionaryGroups";
import DataDictionaryImportExport from "./DataDictionaryImportExport";

export default class DataDictionary extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingDataDictionary: true,
            hasLoadedDataDictionary: false,
            dataDictionary:          null,
            showModal:          false,
            currentVersion:     null,
        };
    }

    componentDidMount() {
        this.getDataDictionary();
    }

    componentDidUpdate(prevProps) {
        const {match} = this.props;

        if (match.params.version !== prevProps.match.params.version) {
            this.getDataDictionary();
        }
    }

    getDataDictionary = () => {
        const {match} = this.props;

        this.setState({
            isLoadingDataDictionary: true,
        });

        axios.get('/api/dictionary/' + match.params.dictionary)
            .then((response) => {
                const versions = response.data.versions.map((version) => {
                    return {value: version.id, label: version.version};
                });

                const currentVersion = (match.params.version && match.params.version !== 'versions') ? match.params.version : versions.slice(-1)[0].label;

                this.setState({
                    dataDictionary:          response.data,
                    isLoadingDataDictionary: false,
                    hasLoadedDataDictionary: true,
                    versions:           versions,
                    currentVersion:     versions.find(({label}) => label === currentVersion),
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataDictionary: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data dictionary';
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
        const {dataDictionary, isLoadingDataDictionary, versions, currentVersion} = this.state;

        if (!dataDictionary && isLoadingDataDictionary) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <DocumentTitle title={`FDP Admin | Data Dictionary | ${dataDictionary.title}`}/>

            <SideBar
                location={location}
                onVersionChange={this.handleVersionChange}
                items={[
                    {
                        to: '/admin/dictionary/' + dataDictionary.id,
                        exact: true,
                        title: 'Data dictionary',
                        icon: 'summary'
                    },
                    {
                        to: '/admin/dictionary/' + dataDictionary.id + '/versions',
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
                        to: '/admin/dictionary/' + dataDictionary.id + '/' + currentVersion.label + '/groups',
                        exact: true,
                        title: 'Groups',
                        customIcon: 'modules'
                    },
                    {
                        to: '/admin/dictionary/' + dataDictionary.id + '/' + currentVersion.label + '/option-groups',
                        exact: true,
                        title: 'Option groups',
                        icon: 'checkboxes'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/admin/dictionary/' + dataDictionary.id + '/' + currentVersion.label + '/import-export',
                        exact: true,
                        title: 'Import/export',
                        icon: 'upload'
                    },
                ]}
            />

            <div className="Page">
                <div className="PageTitle">
                    <ViewHeader>{dataDictionary.title}</ViewHeader>
                </div>

                {isLoadingDataDictionary ? <InlineLoader/> : <Switch>
                    <Route path="/admin/dictionary/:dictionary" exact
                           render={(props) => <DataDictionaryDetails {...props} dataDictionary={dataDictionary}
                                                                version={currentVersion.value}/>}/>
                    <Route path="/admin/dictionary/:dictionary/versions" exact
                           render={(props) => <DataDictionaryVersions {...props} getDataDictionary={this.getDataDictionary}
                                                                 dataDictionary={dataDictionary}
                                                                 version={currentVersion.value}/>}/>
                    <Route path="/admin/dictionary/:dictionary/:version/groups" exact
                           render={(props) => <DataDictionaryGroups {...props} dataDictionary={dataDictionary}
                                                                version={currentVersion.value}/>}/>
                    {/*<Route path="/admin/dictionary/:dictionary/:version/nodes" exact*/}
                    {/*       render={(props) => <DataDictionaryNodes {...props} dataDictionary={dataDictionary}*/}
                    {/*                                          version={currentVersion.value}/>}/>*/}
                    {/*<Route path="/admin/dictionary/:dictionary/:version/prefixes" exact*/}
                    {/*       render={(props) => <DataDictionaryPrefixes {...props} dataDictionary={dataDictionary}*/}
                    {/*                                             version={currentVersion.value}/>}/>*/}
                    {/*<Route path="/admin/dictionary/:dictionary/:version/preview" exact*/}
                    {/*       render={(props) => <DataDictionaryPreview {...props} dataDictionary={dataDictionary}*/}
                    {/*                                            version={currentVersion.value}/>}/>*/}
                    <Route path="/admin/dictionary/:dictionary/:version/import-export" exact
                           render={(props) => <DataDictionaryImportExport {...props} dataDictionary={dataDictionary}
                                                                     getDataDictionary={this.getDataDictionary}
                                                                     version={currentVersion.value}/>}/>

                    <Route component={NotFound}/>
                </Switch>}
            </div>
        </div>;
    }
}