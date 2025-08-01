import { Metadata } from 'types/Metadata';
import React from 'react';
import MetadataItem from 'components/MetadataItem';

interface MetadataDescriptionProps {
    metadata: Metadata;
}

const MetadataDescription: React.FC<MetadataDescriptionProps> = ({ metadata }) => {
    return (
        <div className="InformationDescription">
            {metadata.contents.description.map(item => {
                return <MetadataItem
                    key={`description-${item.order}`}
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
