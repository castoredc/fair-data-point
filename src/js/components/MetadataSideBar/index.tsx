import { Metadata } from 'types/Metadata';
import React, { useState } from 'react';
import { Button, Modal, Separator, Tooltip } from '@castoredc/matter';
import moment from 'moment/moment';
import MetadataItemContainer from 'components/MetadataItem/MetadataItemContainer';
import MetadataItem from 'components/MetadataItem';

interface MetadataSideBarProps {
    metadata: Metadata,
    title: string,
}

const MetadataSideBar: React.FC<MetadataSideBarProps> = ({ metadata, title }) => {
    const [isOpen, setOpen] = useState(false);

    return (
        <div className="MetadataSideBar">
            {metadata.contents.sidebar.map((item) => {
                return <MetadataItem title={item.title} type={item.type} dataType={item.dataType} value={item.value} />
            })}

            <Button icon="openNewWindow" buttonType="bare" onClick={() => setOpen(true)}>
                View metadata
            </Button>

            <Modal accessibleName="Test" open={isOpen} title={`Metadata for ${title}`} onClose={() => setOpen(false)}>

                {metadata.contents.modal.map((item) => {
                    return <MetadataItem
                        key={`modal-${item.order}`}
                        title={item.title}
                        type={item.type}
                        dataType={item.dataType}
                        value={item.value}
                        table
                    />;
                })}

                {metadata.contents.modal.length > 0 && <Separator spacing="comfortable" />}

                <MetadataItemContainer label="Metadata version" table>
                    {metadata.version}
                </MetadataItemContainer>

                <MetadataItemContainer label="Created" table>
                    <Tooltip content={moment(metadata.createdAt).format('DD-MM-YYYY HH:mm:ss')}>
                        {moment(metadata.createdAt).format('DD-MM-YYYY')}
                    </Tooltip>
                </MetadataItemContainer>
                <MetadataItemContainer label="Modified" table>
                    <Tooltip content={moment(metadata.modifiedAt).format('DD-MM-YYYY HH:mm:ss')}>
                        {moment(metadata.modifiedAt).format('DD-MM-YYYY')}
                    </Tooltip>
                </MetadataItemContainer>
            </Modal>
        </div>
    );
};

export default MetadataSideBar;