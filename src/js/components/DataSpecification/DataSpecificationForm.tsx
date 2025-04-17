import React, { FC } from 'react';
import { MetadataFieldType } from 'components/MetadataItem/EnumMappings';
import Stack from '@mui/material/Stack';
import Button from '@mui/material/Button';
import EditIcon from '@mui/icons-material/Edit';
import AddIcon from '@mui/icons-material/Add';
import DataGrid from 'components/DataTable/DataGrid';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import CheckIcon from '@mui/icons-material/Check';

export interface DataSpecificationFormProps {
    fields: any[];
    openFormModal: () => void;
    openFieldModal: (field) => void;
    openRemoveFieldModal: (field) => void;
    optionGroups: any[];
    nodes: any;
}

const DataSpecificationForm: FC<DataSpecificationFormProps> = ({
                                                                   fields,
                                                                   openFormModal,
                                                                   openFieldModal,
                                                                   openRemoveFieldModal,
                                                                   optionGroups,
                                                                   nodes,
                                                               }) => {
    return (
        <div className="DataSpecificationModule">
            <div className="ButtonBar">
                <Stack direction="row" sx={{ justifyContent: 'flex-end', pb: 2 }} spacing={1}>
                    <Button startIcon={<EditIcon />} variant="outlined" onClick={openFormModal}>
                        Edit form
                    </Button>
                    <Button
                        variant="contained"
                        startIcon={<AddIcon />}
                        onClick={() => {
                            openFieldModal(null);
                        }}
                    >
                        Add field
                    </Button>
                </Stack>
            </div>

            <div className="DataSpecificationTable LargeTable">
                <DataGrid
                    disableRowSelectionOnClick
                    accessibleName="Internal nodes"
                    emptyStateContent={`This form does not contain fields`}
                    rows={fields.map(field => {
                        const optionGroup = field.optionGroup ? optionGroups.find(optionGroup => optionGroup.id === field.optionGroup) : null;
                        const node = nodes.value.find(node => node.id === field.node);

                        return {
                            id: field.id,
                            order: field.order,
                            title: field.title,
                            node: node.title,
                            fieldType: MetadataFieldType[field.fieldType],
                            optionGroup: optionGroup ? optionGroup.title : '',
                            required: field.isRequired,
                            data: field,
                        };
                    })}
                    columns={[
                        {
                            headerName: '',
                            field: 'order',
                            maxWidth: 34,
                            minWidth: 34,
                            width: 34,
                        },
                        {
                            headerName: 'Title',
                            field: 'title',
                        },
                        {
                            headerName: 'Node',
                            field: 'node',
                        },
                        {
                            headerName: 'Field type',
                            field: 'fieldType',
                        },
                        {
                            headerName: 'Option group',
                            field: 'optionGroup',
                        },
                        {
                            headerName: 'Required',
                            field: 'required',
                            resizable: false,
                            // isInteractive: true,
                            width: 80,
                            renderCell: (params) => {
                                return params.row.isRequired ? <CheckIcon /> : '';
                            },
                        },
                        {
                            field: 'actions',
                            headerName: '',
                            flex: 1,
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
                                                openFieldModal(params.row.data);
                                            },
                                            label: 'Edit field',
                                        },
                                        {
                                            destination: () => {
                                                openRemoveFieldModal(params.row.data);
                                            },
                                            label: 'Delete field',
                                        },
                                    ]}
                                />;
                            },
                        },
                    ]}
                />
            </div>
        </div>
    );
};

export default DataSpecificationForm;
