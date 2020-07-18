import React, {Component} from 'react'
import DataModelModuleModal from "../../../modals/DataModelModuleModal";
import {Button, Stack} from "@castoredc/matter";
import './DataModelModules.scss';
import axios from "axios";
import {toast} from "react-toastify";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DataModelModule from "../../../components/DataModelModule/DataModelModule";
import ToastContent from "../../../components/ToastContent";
import TripleModal from "../../../modals/TripleModal";
import ConfirmModal from "../../../modals/ConfirmModal";
import SideTabs from "../../../components/SideTabs";
import Toggle from "../../../components/Toggle";

export default class DataModelModules extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                triple: false,
                removeTriple: false,
                module: false
            },
            tripleModalData:      null,
            moduleModalData:      null,
            isLoadingModules:     true,
            hasLoadedModules:     false,
            modules:              [],
            isLoadingNodes:       true,
            hasLoadedNodes:       false,
            nodes:                [],
            isLoadingPrefixes:    true,
            hasLoadedPrefixes:    false,
            prefixes:             [],
            currentModule:      null
        };
    }

    componentDidMount() {
        this.getModules();
        this.getNodes();
        this.getPrefixes();
    }

    getModules = () => {
        const { dataModel, version } = this.props;

        this.setState({
            isLoadingModules: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/module')
            .then((response) => {
                this.setState({
                    modules:          response.data,
                    isLoadingModules: false,
                    hasLoadedModules: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingModules: false,
                });
            });
    };

    getNodes = () => {
        const { dataModel, version } = this.props;

        this.setState({
            isLoadingNodes: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/node')
            .then((response) => {
                this.setState({
                    nodes:          response.data,
                    isLoadingNodes: false,
                    hasLoadedNodes: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingNodes: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the nodes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getPrefixes = () => {
        const {dataModel, version} = this.props;

        this.setState({
            isLoadingPrefixes: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/prefix')
            .then((response) => {
                this.setState({
                    prefixes:          response.data,
                    isLoadingPrefixes: false,
                    hasLoadedPrefixes: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingPrefixes: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the prefixes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };
    
    getOrderOptions = () => {
        const { modules, currentModule } = this.state;

        let order = [{value: 1, label: 'At the beginning of the data model'}];

        if (modules.length === 0) {
            return order;
        }

        for (let i = 0; i < modules.length; i++) {
            const item = modules[i];

            if(currentModule === null || (currentModule && item.id !== currentModule.id)) {
                const moduleNumber = (i + 1);
                order.push({
                    value: (moduleNumber + 1),
                    label: 'After Module ' + moduleNumber + ' (' + item.title + ')'
                });
            }
        }

        return order;
    };

    openModal = (type) => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true
            }
        });
    };

    closeModal = (type) => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false
            }
        });
    };

    openModuleModal = (moduleData) => {
        this.setState({
            currentModule: moduleData ? moduleData : null,
            moduleModalData: moduleData
        }, () => {
            this.openModal('module');
        });
    };

    openTripleModal = (module, tripleData) => {
        this.setState({
            currentModule: module,
            tripleModalData: tripleData
        }, () => {
            this.openModal('triple')
        });
    };

    openRemoveTripleModal = (module, tripleData) => {
        this.setState({
            currentModule: module,
            tripleModalData: tripleData
        }, () => {
            this.openModal('removeTriple')
        });
    };

    onModuleSaved = () => {
        this.closeModal('module');
        this.getModules();
    };

    onTripleSaved = () => {
        this.closeModal('triple');
        this.getModules();
    };

    removeTriple = () => {
        const { dataModel, version } = this.props;
        const { currentModule, tripleModalData } = this.state;

        axios.delete('/api/model/' + dataModel.id + '/v/' + version + '/module/' + currentModule.id + '/triple/' + tripleModalData.id )
            .then(() => {
                this.closeModal('removeTriple');
                this.getModules();
            })
            .catch((error) => {
                toast.error(<ToastContent type="error" message="An error occurred"/>, {
                    position: "top-center"
                });
            });
    };

    render() {
        const { dataModel, version } = this.props;
        const { showModal, hasLoadedModules, hasLoadedNodes, hasLoadedPrefixes, modules, nodes, prefixes, currentModule, moduleModalData, tripleModalData } = this.state;

        if (!hasLoadedModules || !hasLoadedNodes || !hasLoadedPrefixes) {
            return <InlineLoader />;
        }

        const orderOptions = this.getOrderOptions();

        return <div className="PageBody">
            <DataModelModuleModal
                orderOptions={orderOptions}
                show={showModal.module}
                handleClose={() => { this.closeModal('module')}}
                onSaved={this.onModuleSaved}
                modelId={dataModel.id}
                versionId={version}
                data={moduleModalData}
            />

            <TripleModal
                show={showModal.triple}
                handleClose={() => { this.closeModal('triple')}}
                onSaved={this.onTripleSaved}
                modelId={dataModel.id}
                versionId={version}
                module={currentModule}
                nodes={nodes}
                data={tripleModalData}
                prefixes={prefixes}
            />

            <ConfirmModal
                title="Delete triple"
                action="Delete triple"
                variant="danger"
                onConfirm={this.removeTriple}
                onCancel={() => this.closeModal('removeTriple')}
                show={showModal.removeTriple}
            >
                Are you sure you want to delete this triple?
            </ConfirmModal>

            {modules.length === 0 ? <div className="NoResults">This data model does not have modules.</div> : <SideTabs
                hasButtons
                tabs={modules.map((element) => {
                    return {
                        title: `Module ${element.order}. ${element.title}`,
                        badge: element.repeated && 'Repeated',
                        content: <DataModelModule
                                     key={element.id}
                                     id={element.id}
                                     title={element.title}
                                     repeated={element.repeated}
                                     order={element.order}
                                     groupedTriples={element.groupedTriples}
                                     modelId={dataModel.id}
                                     versionId={version}
                                     openAddModuleModal={() => {this.openModuleModal(null)}}
                                     openModuleModal={() => this.openModuleModal({
                                         id:       element.id,
                                         title:    element.title,
                                         order:    element.order,
                                         repeated: element.repeated
                                     })}
                                     openTripleModal={(tripleData) => this.openTripleModal(element, tripleData)}
                                     openRemoveTripleModal={(tripleData) => this.openRemoveTripleModal(element, tripleData)}
                                 />
                    }
                })}
                />
            }
        </div>;
    }
}