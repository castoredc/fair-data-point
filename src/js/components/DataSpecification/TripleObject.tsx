import React, { useRef } from 'react';
import { TripleObjectProps } from './types';
import { Node } from './Node';
import { RowActionsMenu } from 'components/DataTable/RowActionsMenu';

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
        <div className="DataSpecificationObject">
            {Node(title, type, value, repeated)}

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
        </div>
    );
};

export default TripleObject;
