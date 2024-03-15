import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { ActionsCell, Button, CellText, DataGrid, Stack, ToastMessage } from '@castoredc/matter';
import OptionGroupModal from 'modals/DataSpecification/OptionGroupModal';
import ConfirmModal from 'modals/ConfirmModal';
import DataGridContainer from 'components/DataTable/DataGridContainer';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';

interface OptionGroupsProps extends AuthorizedRouteComponentProps {
    type: string;
    optionGroups: any;
    getOptionGroups: () => void;
    dataSpecification: any;
    version: any;
}

interface OptionGroupsState {
    showModal: any;
    optionGroupModalData: any;
}

export default class OptionGroups extends Component<OptionGroupsProps, OptionGroupsState> {
    private tableRef: React.RefObject<unknown>;

    constructor(props) {
        super(props);
        this.state = {
            showModal: {
                add: false,
                remove: false,
            },
            optionGroupModalData: null,
        };

        this.tableRef = React.createRef();
    }

    openModal = (type, data) => {
        const { showModal } = this.state;

        this.setState({
            showModal: {
                ...showModal,
                [type]: true,
            },
            optionGroupModalData: data,
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
        const { getOptionGroups } = this.props;
        this.closeModal(type);
        getOptionGroups();
    };

    removeOptionGroup = () => {
        const { type, dataSpecification, version } = this.props;
        const { optionGroupModalData } = this.state;

        apiClient
            .delete('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/option-group/' + optionGroupModalData.id)
            .then(() => {
                toast.success(
                    <ToastMessage
                        type="success"
                        title={`The option group was successfully removed`}
                    />,
                    {
                        position: 'top-right',
                    }
                );

                this.onSaved('remove');
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);
            });
    };

    render() {
        const { showModal, optionGroupModalData } = this.state;
        const { type, dataSpecification, optionGroups, version } = this.props;

        const columns = [
            {
                Header: 'Name',
                accessor: 'title',
            },
            {
                accessor: 'menu',
                disableGroupBy: true,
                disableResizing: true,
                isInteractive: true,
                isSticky: true,
                maxWidth: 34,
                minWidth: 34,
                width: 34,
            },
        ];

        const rows = optionGroups.map(item => {
            const data = {
                id: item.id,
                title: item.title,
                description: item.description,
                options: item.options,
            };

            return {
                title: <CellText>{item.title}</CellText>,
                menu: (
                    <ActionsCell
                        items={[
                            {
                                destination: () => {
                                    this.openModal('add', data);
                                },
                                label: 'Edit option group',
                            },
                            {
                                destination: () => {
                                    this.openModal('remove', data);
                                },
                                label: 'Delete option group',
                            },
                        ]}
                    />
                ),
            };
        });

        return (
            <PageBody>
                <OptionGroupModal
                    type={type}
                    show={showModal.add}
                    handleClose={() => {
                        this.closeModal('add');
                    }}
                    onSaved={() => {
                        this.onSaved('add');
                    }}
                    modelId={dataSpecification.id}
                    versionId={version}
                    data={optionGroupModalData}
                />

                {optionGroupModalData && (
                    <ConfirmModal
                        title="Delete option group"
                        action="Delete option group"
                        variant="danger"
                        onConfirm={this.removeOptionGroup}
                        onCancel={() => {
                            this.closeModal('remove');
                        }}
                        show={showModal.remove}
                    >
                        Are you sure you want to delete option group <strong>{optionGroupModalData.title}</strong>?
                    </ConfirmModal>
                )}

                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button
                            icon="add"
                            onClick={() => {
                                this.openModal('add', null);
                            }}
                        >
                            Add option group
                        </Button>
                    </Stack>
                </div>

                <DataGridContainer fullHeight forwardRef={this.tableRef}>
                    <DataGrid
                        accessibleName="OptionGroups"
                        anchorRightColumns={1}
                        emptyStateContent={`This ${getType(type)} does not have option groups`}
                        rows={rows}
                        columns={columns}
                    />
                </DataGridContainer>
            </PageBody>
        );
    }
}
