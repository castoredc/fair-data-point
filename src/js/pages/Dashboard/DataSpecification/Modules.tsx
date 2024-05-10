import React, { Component } from 'react';
import './DataSpecificationModules.scss';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { Button } from '@castoredc/matter';
import DataSpecificationModuleModal from 'modals/DataSpecificationModuleModal';
import TripleModal from 'modals/TripleModal';
import ConfirmModal from 'modals/ConfirmModal';
import SideTabs from 'components/SideTabs';
import DataSpecificationModule from 'src/js/components/DataSpecification/DataSpecificationModule';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';

interface ModulesProps extends AuthorizedRouteComponentProps {
    modules: any;
    nodes: any;
    prefixes: any;
    getModules: () => void;
    dataSpecification: any;
    version: any;
    type: string;
}

interface ModulesState {
    showModal: any;
    tripleModalData: any;
    moduleModalData: any;
    currentModule: any;
}

export default class Modules extends Component<ModulesProps, ModulesState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                triple: false,
                removeTriple: false,
                module: false,
            },
            tripleModalData: null,
            moduleModalData: null,
            currentModule: null,
        };
    }

    getOrderOptions = () => {
        const { modules, type } = this.props;
        const { currentModule } = this.state;

        let order = [{
            value: 1,
            label: 'At the beginning of the ' + getType(type),
        }];

        if (modules.length === 0) {
            return order;
        }

        for (let i = 0; i < modules.length; i++) {
            const item = modules[i];

            if (currentModule === null || (currentModule && item.id !== currentModule.id)) {
                const moduleNumber = i + 1;
                order.push({
                    value: moduleNumber + 1,
                    label: 'After Group ' + moduleNumber + ' (' + item.title + ')',
                });
            }
        }

        return order;
    };

    openModal = type => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
        });
    };

    closeModal = type => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: false,
            },
        });
    };

    openModuleModal = moduleData => {
        this.setState(
            {
                currentModule: moduleData ? moduleData : null,
                moduleModalData: moduleData,
            },
            () => {
                this.openModal('module');
            }
        );
    };

    openTripleModal = (module, tripleData) => {
        this.setState(
            {
                currentModule: module,
                tripleModalData: tripleData,
            },
            () => {
                this.openModal('triple');
            }
        );
    };

    openRemoveTripleModal = (module, tripleData) => {
        this.setState(
            {
                currentModule: module,
                tripleModalData: tripleData,
            },
            () => {
                this.openModal('removeTriple');
            }
        );
    };

    onModuleSaved = () => {
        const { getModules } = this.props;
        this.closeModal('module');
        getModules();
    };

    onTripleSaved = () => {
        const { getModules } = this.props;
        this.closeModal('triple');
        getModules();
    };

    removeTriple = () => {
        const { type, dataSpecification, version, getModules } = this.props;
        const { currentModule, tripleModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/module/' + currentModule.id + '/triple/' + tripleModalData.id)
            .then(() => {
                this.closeModal('removeTriple');
                getModules();
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    render() {
        const { type, dataSpecification, version, modules, nodes, prefixes } = this.props;
        const { showModal, currentModule, moduleModalData, tripleModalData } = this.state;

        if (nodes === null) {
            return null;
        }

        const orderOptions = this.getOrderOptions();

        return (
            <PageBody>
                <DataSpecificationModuleModal
                    type={type}
                    orderOptions={orderOptions}
                    show={showModal.module}
                    handleClose={() => {
                        this.closeModal('module');
                    }}
                    onSaved={this.onModuleSaved}
                    modelId={dataSpecification.id}
                    versionId={version}
                    data={moduleModalData}
                    valueNodes={nodes.value}
                    prefixes={prefixes}
                />

                <TripleModal
                    type={type}
                    show={showModal.triple}
                    handleClose={() => {
                        this.closeModal('triple');
                    }}
                    onSaved={this.onTripleSaved}
                    modelId={dataSpecification.id}
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

                {modules.length === 0 ? (
                    <div className="NoResults">
                        This {getType(type)} does not have any groups.
                        <br />
                        <br />
                        <Button icon="add" onClick={() => this.openModuleModal(null)}>
                            Add group
                        </Button>
                    </div>
                ) : (
                    <SideTabs
                        hasButtons
                        title="Groups"
                        actions={<Button icon="add" iconDescription="Add group" onClick={() => this.openModuleModal(null)} />}
                        tabs={modules.map(element => {
                            let icons = [] as any;

                            if (element.repeated) {
                                icons.push({
                                    icon: 'copy',
                                    title: 'This group is repeated',
                                });
                            }

                            if (element.dependent) {
                                icons.push({
                                    icon: 'decision',
                                    title: 'This group is dependent',
                                });
                            }

                            return {
                                number: element.order,
                                title: element.title,
                                icons: icons,
                                content: (
                                    <DataSpecificationModule
                                        key={element.id}
                                        id={element.id}
                                        title={element.title}
                                        repeated={element.repeated}
                                        order={element.order}
                                        groupedTriples={element.groupedTriples}
                                        modelId={dataSpecification.id}
                                        versionId={version}
                                        openModuleModal={() =>
                                            this.openModuleModal({
                                                id: element.id,
                                                title: element.title,
                                                order: element.order,
                                                repeated: element.repeated,
                                                dependent: element.dependent,
                                                dependencies: element.dependencies,
                                            })
                                        }
                                        openTripleModal={tripleData => this.openTripleModal(element, tripleData)}
                                        openRemoveTripleModal={tripleData => this.openRemoveTripleModal(element, tripleData)}
                                    />
                                ),
                            };
                        })}
                    />
                )}
            </PageBody>
        );
    }
}
