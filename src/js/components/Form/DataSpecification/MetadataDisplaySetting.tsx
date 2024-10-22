import { Button, DataGrid, FormLabel, Space, Stack } from '@castoredc/matter';
import React from 'react';

interface MetadataDisplaySettingProps {
    position: string;
    items: any;
    openModal: () => void;
}

const MetadataDisplaySetting: React.FC<MetadataDisplaySettingProps> = ({ position, items, openModal }) => {
    return (
        <div>
            <FormLabel>{position}</FormLabel>

            <Space bottom="default" />

            {items.length > 0 ? (
                <DataGrid
                    accessibleName="Title"
                    emptyStateContent={`This position does not have items`}
                    rows={items}
                    anchorRightColumns={1}
                    columns={[
                        {
                            Header: 'Title',
                            accessor: 'title',
                        },
                        {
                            Header: 'Node',
                            accessor: 'node',
                        },
                        {
                            Header: 'Display type',
                            accessor: 'type',
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
            ) : (
                <div>This position does not have items</div>
            )}

            <Space bottom="default" />

            <Stack distribution="trailing" alignment="end">
                <Button icon="add" onClick={openModal}>
                    Add item
                </Button>
            </Stack>
        </div>
    );
};

export default MetadataDisplaySetting;
