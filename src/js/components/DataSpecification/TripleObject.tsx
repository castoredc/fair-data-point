import React, { useRef } from 'react';
import { ActionMenu } from '@castoredc/matter';
import { TripleObjectProps } from './types';
import { Node } from './Node';

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
                <ActionMenu
                    accessibleLabel="Contextual menu"
                    container={ref.current !== null ? ref.current : undefined}
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
