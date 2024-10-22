import React, { Component } from 'react';
import { ActionsCell, CellText, LoadingOverlay, Separator, ToastMessage } from '@castoredc/matter';
import ConfirmModal from 'modals/ConfirmModal';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import PageTabs from 'components/PageTabs';
import { MetadataDisplayType, ResourceType } from 'components/MetadataItem/EnumMappings';
import MetadataDisplaySetting from 'components/Form/DataSpecification/MetadataDisplaySetting';
import { Types } from 'types/Types';
import DisplaySettingModal from 'modals/DisplaySettingModal';

interface DisplayProps extends AuthorizedRouteComponentProps {
    type: string;
    nodes: any;
    displaySettings: any;
    getDisplaySettings: () => void;
    dataSpecification: any;
    version: any;
    types: Types;
}

interface DisplayState {
    showModal: any;
    modalData: any;
}

export default class Display extends Component<DisplayProps, DisplayState> {
    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            modalData: null,
        };
    }

    openModal = (type, data, position) => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: position,
            },
            modalData: data,
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

    onSaved = type => {
        const { getDisplaySettings } = this.props;

        this.closeModal(type);

        getDisplaySettings();
    };

    removeItem = () => {
        const { type, dataSpecification, version } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete('/api/metadata-model/' + dataSpecification.id + '/v/' + version.value + '/display' + `/${modalData.id}`)
            .then(() => {
                toast.success(<ToastMessage type="success" title={`The item was successfully removed`} />, {
                    position: 'top-right',
                });

                this.onSaved('remove');
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);

                    this.onSaved('remove');
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    render() {
        const { showModal, modalData } = this.state;
        const { type, dataSpecification, nodes, displaySettings, version, history, match, types } = this.props;

        if (displaySettings === null || types.dataTypes.length === 0) {
            return <LoadingOverlay accessibleLabel="Loading data model" />;
        }

        let rows = {};

        Object.keys(displaySettings).map(resourceType => {
            rows[resourceType] = {};

            Object.keys(displaySettings[resourceType]).map(position => {
                rows[resourceType][position] = displaySettings[resourceType][position].map(item => {
                    const node = nodes.value.find(node => node.id === item.node);

                    return {
                        title: <CellText>{item.title}</CellText>,
                        node: <CellText>{node.title}</CellText>,
                        type: <CellText>{MetadataDisplayType[item.type]}</CellText>,
                        menu: (
                            <ActionsCell
                                items={[
                                    {
                                        destination: () => {
                                            this.openModal(
                                                'add',
                                                {
                                                    id: item.id,
                                                    title: item.title,
                                                    node: item.node,
                                                    order: item.order,
                                                    displayType: item.type,
                                                    position: item.position,
                                                    resourceType: item.resourceType,
                                                },
                                                position
                                            );
                                        },
                                        label: 'Edit item',
                                    },
                                    {
                                        destination: () => {
                                            this.openModal(
                                                'remove',
                                                {
                                                    id: item.id,
                                                    title: item.title,
                                                    node: item.node,
                                                    order: item.order,
                                                    displayType: item.type,
                                                    position: item.position,
                                                    resourceType: item.resourceType,
                                                },
                                                position
                                            );
                                        },
                                        label: 'Delete item',
                                    },
                                ]}
                            />
                        ),
                    };
                });
            });
        });

        let tabs = {};

        Object.keys(displaySettings).forEach(resourceType => {
            tabs[resourceType] = {
                title: ResourceType[resourceType],
                content: (
                    <div>
                        <MetadataDisplaySetting
                            position="Title"
                            items={rows[resourceType].title}
                            openModal={() => this.openModal('add', null, 'title')}
                        />
                        <Separator spacing="comfortable" />
                        <MetadataDisplaySetting
                            position="Description"
                            items={rows[resourceType].description}
                            openModal={() => this.openModal('add', null, 'description')}
                        />
                        <Separator spacing="comfortable" />
                        <MetadataDisplaySetting
                            position="Sidebar"
                            items={rows[resourceType].sidebar}
                            openModal={() => this.openModal('add', null, 'sidebar')}
                        />
                        <Separator spacing="comfortable" />
                        <MetadataDisplaySetting
                            position="Modal"
                            items={rows[resourceType].modal}
                            openModal={() => this.openModal('add', null, 'modal')}
                        />
                    </div>
                ),
            };
        });

        const resourceType = match.params.resourceType;

        return (
            <PageBody>
                <DisplaySettingModal
                    open={!!showModal.add}
                    onClose={() => this.closeModal('add')}
                    onSaved={() => this.onSaved('add')}
                    modelId={dataSpecification.id}
                    versionId={version.value}
                    data={modalData}
                    types={types}
                    nodes={nodes}
                    items={showModal.add ? displaySettings[resourceType][showModal.add] : []}
                    position={showModal.add}
                    resourceType={resourceType}
                />

                {modalData && (
                    <ConfirmModal
                        title="Delete item"
                        action="Delete item"
                        variant="danger"
                        onConfirm={this.removeItem}
                        onCancel={() => {
                            this.closeModal('remove');
                        }}
                        show={!!showModal.remove}
                    >
                        Are you sure you want to delete this item?
                    </ConfirmModal>
                )}

                <PageTabs
                    selected={match.params.resourceType}
                    onChange={selectedKey => {
                        const newUrl = `/dashboard/${type}s/${dataSpecification.id}/${version.label}/display/${selectedKey}`;
                        history.push(newUrl);
                    }}
                    tabs={tabs}
                />
            </PageBody>
        );
    }
}
