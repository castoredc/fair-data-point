import { Metadata } from 'types/Metadata';
import React from 'react';
import { classNames } from '../../util';
import MetadataItemContainer from 'components/MetadataItem/MetadataItemContainer';
import Language from 'components/MetadataItem/Language';
import License from 'components/MetadataItem/License';
import { localizedText } from '../../util';
import OntologyConcept from 'components/MetadataItem/OntologyConcept';
import { OpenNewWindowIcon } from '@castoredc/matter-icons';
import Country from 'components/MetadataItem/Country';
import { OntologyConceptType } from 'types/OntologyConceptType';

interface MetadataItemProps {
    title: string,
    type: string,
    dataType: string,
    value: any;
    table?: boolean,
    hideLabel?: boolean,
}

interface MetadataItemValueProps {
    type: string,
    dataType: string,
    value: any
}

const MetadataItemValue: React.FC<MetadataItemValueProps> = ({ type, dataType, value }) => {
    switch (type) {
        case 'heading':
            return <div>
                {dataType === 'langString' && localizedText(value, 'en')}
                {dataType === 'string' && value}
            </div>;
        case 'description':
            console.log('---Description');
            console.log(value);
            return <div>
                {dataType === 'langString' && localizedText(value, 'en')}
                {dataType === 'string' && value}
            </div>;
        case 'paragraph':
            return <div>
                {dataType === 'langString' && localizedText(value, 'en')}
                {dataType === 'string' && value}
            </div>;
        case 'ontologyConcepts':
            return <div>
                {value.map((ontologyConcept: OntologyConceptType) => {
                    return <OntologyConcept
                        code={ontologyConcept.code}
                        displayName={ontologyConcept.displayName}
                        ontology={ontologyConcept.ontology}
                        url={ontologyConcept.url}
                    />
                })}
            </div>;
        case 'date':
            return <div>{value}</div>;
        case 'time':
            return <div>{value}</div>;
        case 'yesNo':
            return <div>
                {value}
            </div>;
        case 'list':
            return <div className="List">
                {value.map((item) => {
                    return item;
                })}
            </div>;
        case 'language':
            return <Language code={value} />;
        case 'license':
            return <License slug={value} />
        case 'country':
            return <div>
                return <Country code={value} />;
            </div>;
        case 'agents':
            return <div></div>;
        case 'image':
            return <div>
                <img src={value} />
            </div>;
        case 'link':
            return <div className="Link">
                <a href={value} target="_blank">{value}</a>
                <OpenNewWindowIcon width="10px" height="10px" />
            </div>;
    }

    return null;
};

const MetadataItem: React.FC<MetadataItemProps> = ({ title, type, dataType, value, table, hideLabel }) => {
    hideLabel = ['image'].includes(type) || hideLabel;

    if(value === null) {
        return null;
    }

    return <MetadataItemContainer label={title} table={table} hideLabel={hideLabel}>
        <MetadataItemValue type={type} dataType={dataType} value={value} />
    </MetadataItemContainer>
}

export default MetadataItem;