import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import ConfirmModal from 'modals/ConfirmModal';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import PageTabs from 'components/PageTabs';
import { MetadataDisplayType, ResourceType } from 'components/MetadataItem/EnumMappings';
import MetadataDisplaySetting from 'components/Form/DataSpecification/MetadataDisplaySetting';
import { Types } from 'types/Types';
import DisplaySettingModal from 'modals/DisplaySettingModal';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { Divider } from '@mui/material';

interface DisplayProps extends AuthorizedRouteComponentProps, ComponentWithNotifications, ComponentWithNotifications {
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

class Display extends Component<DisplayProps, DisplayState> {
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
        const { type, dataSpecification, version, notifications } = this.props;
        const { modalData } = this.state;

        apiClient
            .delete('/api/metadata-model/' + dataSpecification.id + '/v/' + version.value + '/display' + `/${modalData.id}`)
            .then(() => {
                notifications.show(`The item was successfully removed`, {
                    variant: 'success',

                });

                this.onSaved('remove');
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });

                    this.onSaved('remove');
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
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
                        title: item.title,
                        node: node.title,
                        type: MetadataDisplayType[item.type],
                        data: item,
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
                            position="title"
                            label="Title"
                            items={rows[resourceType].title}
                            openModal={this.openModal}
                        />

                        <Divider />

                        <MetadataDisplaySetting
                            position="description"
                            label="Description"
                            items={rows[resourceType].description}
                            openModal={this.openModal}
                        />

                        <Divider />

                        <MetadataDisplaySetting
                            position="sidebar"
                            label="Sidebar"
                            items={rows[resourceType].sidebar}
                            openModal={this.openModal}
                        />

                        <Divider />

                        <MetadataDisplaySetting
                            position="modal"
                            label="Modal"
                            items={rows[resourceType].modal}
                            openModal={this.openModal}
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
                        variant="contained"
                        color="error"
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

export default withNotifications(Display);