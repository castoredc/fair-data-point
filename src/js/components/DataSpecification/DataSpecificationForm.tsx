import React, { FC } from 'react';
import { ActionsCell, Button, CellText, DataGrid, IconCell, Stack } from '@castoredc/matter';
import { MetadataFieldType } from 'components/MetadataItem/EnumMappings';

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
                <Stack distribution="trailing">
                    <Button icon="edit" buttonType="secondary" onClick={openFormModal}>
                        Edit form
                    </Button>
                    <Button
                        icon="add"
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
                    accessibleName="Internal nodes"
                    emptyStateContent={`This form does not contain fields`}
                    rows={fields.map(field => {
                        const optionGroup = field.optionGroup ? optionGroups.find(optionGroup => optionGroup.id === field.optionGroup) : null;
                        const node = nodes.value.find(node => node.id === field.node);

                        return {
                            order: <CellText>{field.order}</CellText>,
                            title: <CellText>{field.title}</CellText>,
                            node: <CellText>{node.title}</CellText>,
                            fieldType: <CellText>{MetadataFieldType[field.fieldType]}</CellText>,
                            optionGroup: <CellText>{optionGroup ? optionGroup.title : ''}</CellText>,
                            required: field.isRequired ? <IconCell icon={{ type: 'tickSmall' }} /> : undefined,
                            menu: (
                                <ActionsCell
                                    items={[
                                        {
                                            destination: () => {
                                                openFieldModal(field);
                                            },
                                            label: 'Edit field',
                                        },
                                        {
                                            destination: () => {
                                                openRemoveFieldModal(field);
                                            },
                                            label: 'Delete field',
                                        },
                                    ]}
                                />
                            ),
                        };
                    })}
                    anchorRightColumns={1}
                    columns={[
                        {
                            Header: '',
                            accessor: 'order',
                            maxWidth: 34,
                            minWidth: 34,
                            width: 34,
                        },
                        {
                            Header: 'Title',
                            accessor: 'title',
                        },
                        {
                            Header: 'Node',
                            accessor: 'node',
                        },
                        {
                            Header: 'Field type',
                            accessor: 'fieldType',
                        },
                        {
                            Header: 'Option group',
                            accessor: 'optionGroup',
                        },
                        {
                            Header: 'Required',
                            accessor: 'required',
                            disableResizing: true,
                            isInteractive: true,
                            width: 80,
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
                    ]}
                />
            </div>
        </div>
    );
};

export default DataSpecificationForm;
