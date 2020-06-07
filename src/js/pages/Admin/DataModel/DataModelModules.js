import React, {Component} from 'react'
import AddDataModelModuleModal from "../../../modals/AddDataModelModuleModal";
import {Col, Row} from "react-bootstrap";
import {Button} from "@castoredc/matter";
import './DataModelModules.scss';
import axios from "axios";
import {toast} from "react-toastify";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DataModelModule from "../../../components/DataModelModule/DataModelModule";
import ToastContent from "../../../components/ToastContent";
import AddTripleModal from "../../../modals/AddTripleModal";

export default class DataModelModules extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:            {
                triple: false,
                module: false
            },
            isLoadingModules:     true,
            hasLoadedModules:     false,
            modules:              [],
            isLoadingNodes:       true,
            hasLoadedNodes:       false,
            nodes:                [],
        };
    }

    componentDidMount() {
        this.getModules();
        this.getNodes();
    }

    getModules = () => {
        const { dataModel } = this.props;

        this.setState({
            isLoadingModules: true,
        });

        axios.get('/api/model/' + dataModel.id + '/module')
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
        const { dataModel } = this.props;

        this.setState({
            isLoadingNodes: true,
        });

        axios.get('/api/model/' + dataModel.id + '/node')
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the nodes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getOrderOptions = () => {
        const { modules } = this.state;

        let order = [{value: 1, label: 'At the beginning of the data model'}];

        if (modules.length === 0) {
            return order;
        }

        for (let i = 0; i < modules.length; i++) {
            const item = modules[i];
            const moduleNumber = (i + 1);
            order.push({value: (moduleNumber + 1), label: 'After Module ' + moduleNumber + ' (' + item.title + ')'});
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

    openModuleModal = () => {
        this.openModal('module');
    };

    openTripleModal = (moduleId) => {
        this.setState({
            currentModuleId: moduleId
        }, () => {
            this.openModal('triple')
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

    render() {
        const { dataModel } = this.props;
        const { showModal, hasLoadedModules, hasLoadedNodes, modules, nodes, currentModuleId } = this.state;

        if (!hasLoadedModules || !hasLoadedNodes) {
            return <InlineLoader />;
        }

        const orderOptions = this.getOrderOptions();

        return <div>
            <AddDataModelModuleModal
                orderOptions={orderOptions}
                show={showModal.module}
                handleClose={() => { this.closeModal('module')}}
                onSaved={this.onModuleSaved}
                modelId={dataModel.id}
            />

            <AddTripleModal
                show={showModal.triple}
                handleClose={() => { this.closeModal('triple')}}
                onSaved={this.onTripleSaved}
                modelId={dataModel.id}
                moduleId={currentModuleId}
                nodes={nodes}
            />

            <Row>
                <Col sm={6} />
                <Col sm={6}>
                    <div className="ButtonBar Right">
                        <Button icon="add" onClick={this.openModuleModal}>Add module</Button>
                    </div>
                </Col>
            </Row>
            <Row>
                <Col sm={12}>
                    {modules.length === 0 ? <div className="NoResults">This data model does not have modules.</div> : modules.map((element) => {
                        return <DataModelModule
                            key={element.id}
                            id={element.id}
                            title={element.title}
                            order={element.order}
                            groupedTriples={element.groupedTriples}
                            modelId={dataModel.id}
                            openModal={() => this.openTripleModal(element.id)}
                        />;
                    })}
                </Col>
            </Row>
        </div>;
    }
}