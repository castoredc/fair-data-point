import { Metadata } from 'types/Metadata';
import React, { useState } from 'react';
import Button from '@mui/material/Button';
import moment from 'moment/moment';
import MetadataItemContainer from 'components/MetadataItem/MetadataItemContainer';
import MetadataItem from 'components/MetadataItem';
import './MetadataSideBar.scss';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import Modal from 'components/Modal';
import Tooltip from '@mui/material/Tooltip';
import { Divider } from '@mui/material';

interface MetadataSideBarProps {
    metadata: Metadata;
    title: string;
}

const MetadataSideBar: React.FC<MetadataSideBarProps> = ({ metadata, title }) => {
    const [isOpen, setOpen] = useState(false);

    return (
        <div className="MetadataSideBar">
            {metadata.contents.sidebar.map(item => {
                return <MetadataItem
                    key={`sidebar-${item.order}`}
                    title={item.title}
                    type={item.type}
                    dataType={item.dataType}
                    value={item.value}
                />;
            })}

            <Button startIcon={<OpenInNewIcon />} variant="text" onClick={() => setOpen(true)} className="ViewMetadata">
                View metadata
            </Button>

            <Modal open={isOpen} title={`Metadata for ${title}`} onClose={() => setOpen(false)}>
                {metadata.contents.modal.map(item => {
                    return (
                        <MetadataItem
                            key={`modal-${item.order}`}
                            title={item.title}
                            type={item.type}
                            dataType={item.dataType}
                            value={item.value}
                            table
                        />
                    );
                })}

                {metadata.contents.modal.length > 0 && <Divider />}

                <MetadataItemContainer label="Metadata version" table>
                    {metadata.version}
                </MetadataItemContainer>

                <MetadataItemContainer label="Created" table>
                    <Tooltip title={moment(metadata.createdAt).format('DD-MM-YYYY HH:mm:ss')}>
                        <span>{moment(metadata.createdAt).format('DD-MM-YYYY')}</span>
                    </Tooltip>
                </MetadataItemContainer>
                <MetadataItemContainer label="Modified" table>
                    <Tooltip title={moment(metadata.modifiedAt).format('DD-MM-YYYY HH:mm:ss')}>
                        <span>{moment(metadata.modifiedAt).format('DD-MM-YYYY')}</span>
                    </Tooltip>
                </MetadataItemContainer>
            </Modal>
        </div>
    );
};

export default MetadataSideBar;
