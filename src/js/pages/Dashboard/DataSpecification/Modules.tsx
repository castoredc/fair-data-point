import React, { Component } from 'react';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import DataSpecificationModuleModal from 'modals/DataSpecificationModuleModal';
import TripleModal from 'modals/TripleModal';
import ConfirmModal from 'modals/ConfirmModal';
import SideTabs from 'components/SideTabs';
import DataSpecificationModule from 'src/js/components/DataSpecification/DataSpecificationModule';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';
import IconButton from '@mui/material/IconButton';

interface ModulesProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
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

class Modules extends Component<ModulesProps, ModulesState> {
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

        let order = [
            {
                value: 1,
                label: 'At the beginning of the ' + getType(type),
            },
        ];

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
            },
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
            },
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
            },
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
        const { type, dataSpecification, version, getModules, notifications } = this.props;
        const { currentModule, tripleModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/module/' + currentModule.id + '/triple/' + tripleModalData.id)
            .then(() => {
                this.closeModal('removeTriple');
                getModules();
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });
            });
    };

    render() {
        const { type, dataSpecification, version, modules, nodes, prefixes, match } = this.props;
        const { showModal, currentModule, moduleModalData, tripleModalData } = this.state;

        if (nodes === null) {
            return null;
        }

        const initialTab = match.params.moduleId ? modules.findIndex(module => module.id === match.params.moduleId) : 0;

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
                    variant="contained"
                    color="error"
                    onConfirm={this.removeTriple}
                    onCancel={() => this.closeModal('removeTriple')}
                    show={showModal.removeTriple}
                >
                    Are you sure you want to delete this triple?
                </ConfirmModal>

                {modules.length === 0 ? (
                    <NoResults>
                        This {getType(type)} does not have modules.
                        <br />
                        <br />
                        <Button
                            startIcon={<AddIcon />}
                            onClick={() => this.openModuleModal(null)}
                            variant="contained"
                        >
                            Add module
                        </Button>
                    </NoResults>
                ) : (
                    <SideTabs
                        hasButtons
                        title="Groups"
                        actions={(
                            <IconButton
                                onClick={() => this.openModuleModal(null)}
                            >
                                <AddIcon />
                            </IconButton>
                        )}
                        initialTab={initialTab}
                        url={`/dashboard/${type}s/${dataSpecification.id}/${match.params.version}/modules`}
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
                                id: element.id,
                                title: element.title,
                                badge: element.resourceType ?? null,
                                icons: icons,
                                content: (
                                    <DataSpecificationModule
                                        key={element.id}
                                        groupedTriples={element.groupedTriples}
                                        openModuleModal={() =>
                                            this.openModuleModal({
                                                id: element.id,
                                                title: element.title,
                                                order: element.order,
                                                resourceType: element.resourceType,
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

export default withNotifications(Modules);