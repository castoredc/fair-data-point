import Button from '@mui/material/Button';
import React from 'react';
import AddIcon from '@mui/icons-material/Add';
import Stack from '@mui/material/Stack';
import DataGrid from 'components/DataTable/DataGrid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import { FormLabel } from '@mui/material';

interface MetadataDisplaySettingProps {
    position: string;
    label: string;
    items: any;
    openModal: (type, data, position) => void;
}

const MetadataDisplaySetting: React.FC<MetadataDisplaySettingProps> = ({ position, label, items, openModal }) => {
    return (
        <div>
            <FormLabel>{label}</FormLabel>

            {items.length > 0 ? (
                <DataGrid
                    disableRowSelectionOnClick
                    accessibleName="Title"
                    emptyStateContent={`This position does not have items`}
                    rows={items}
                    columns={[
                        {
                            headerName: 'Title',
                            field: 'title',
                        },
                        {
                            headerName: 'Node',
                            field: 'node',
                        },
                        {
                            headerName: 'Display type',
                            field: 'type',
                        },
                        {
                            field: 'actions',
                            headerName: '',
                            width: 80,
                            sortable: false,
                            disableColumnMenu: true,
                            align: 'right',
                            cellClassName: 'actionsCell',
                            renderCell: (params) => {
                                return <RowActionsMenu
                                    row={params.row}
                                    items={[
                                        {
                                            destination: () => {
                                                openModal(
                                                    'add',
                                                    {
                                                        id: params.row.data.id,
                                                        title: params.row.data.title,
                                                        node: params.row.data.node,
                                                        order: params.row.data.order,
                                                        displayType: params.row.data.type,
                                                        position: params.row.data.position,
                                                        resourceType: params.row.data.resourceType,
                                                    },
                                                    position,
                                                );
                                            },
                                            label: 'Edit item',
                                        },
                                        {
                                            destination: () => {
                                                openModal(
                                                    'remove',
                                                    {
                                                        id: params.row.data.id,
                                                        title: params.row.data.title,
                                                        node: params.row.data.node,
                                                        order: params.row.data.order,
                                                        displayType: params.row.data.type,
                                                        position: params.row.data.position,
                                                        resourceType: params.row.data.resourceType,
                                                    },
                                                    position,
                                                );
                                            },
                                            label: 'Delete item',
                                        },
                                    ]}
                                />;
                            },
                        },
                    ]}
                />
            ) : (
                <div>This position does not have items</div>
            )}

            <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                <Button
                    startIcon={<AddIcon />}
                    onClick={() => openModal('add', null, position)}
                >
                    Add item
                </Button>
            </Stack>
        </div>
    );
};

export default MetadataDisplaySetting;
