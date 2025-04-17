import React, { useRef } from 'react';
import { TripleObjectProps } from './types';
import { Node } from './Node';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';
import Box from '@mui/material/Box';

const TripleObject: React.FC<TripleObjectProps> = props => {
    const { tripleId, id, type, title, repeated, value, openTripleModal, data, openRemoveTripleModal } = props;
    const ref = useRef<HTMLDivElement>(null);

    const newData = {
        ...data,
        objectType: type,
        objectValue: id,
        id: tripleId,
    };

    return (
        <Box
            className="DataSpecificationObject"
            sx={{
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                width: '100%',
            }}
        >
            <div>{Node(title, type, value, repeated)}</div>

            <div className="DataSpecificationTripleActions" ref={ref}>
                <RowActionsMenu
                    items={[
                        {
                            destination: () => {
                                openTripleModal(newData);
                            },
                            label: 'Edit triple',
                        },
                        {
                            destination: () => {
                                openRemoveTripleModal(newData);
                            },
                            label: 'Delete triple',
                        },
                    ]}
                />
            </div>
        </Box>
    );
};

export default TripleObject;
