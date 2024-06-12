import { Metadata } from 'types/Metadata';
import React, { useState } from 'react';
import { Button, Modal, Separator, Tooltip } from '@castoredc/matter';
import moment from 'moment/moment';
import MetadataItemContainer from 'components/MetadataItem/MetadataItemContainer';
import MetadataItem from 'components/MetadataItem';
import { localizedText } from 'utils/jsonLdUtils';

interface MetadataDescriptionProps {
    metadata: Metadata,
}

const MetadataDescription: React.FC<MetadataDescriptionProps> = ({ metadata }) => {

    return (
        <div className="InformationDescription">
            {metadata.contents.description.map((item) => {
                return <MetadataItem
                    title={item.title}
                    type={item.type}
                    dataType={item.dataType}
                    value={item.value}
                    hideLabel
                />;
            })}
        </div>
    );
};

export default MetadataDescription;