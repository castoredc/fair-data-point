import React from 'react';
import { localizedText } from '../../util';
import MetadataItemContainer from 'components/MetadataItem/MetadataItemContainer';
import Language from 'components/MetadataItem/Language';
import License from 'components/MetadataItem/License';
import OntologyConcept from 'components/MetadataItem/OntologyConcept';
import { OpenNewWindowIcon } from '@castoredc/matter-icons';
import Country from 'components/MetadataItem/Country';
import { OntologyConceptType } from 'types/OntologyConceptType';
import moment from 'moment';
import { AgentListItemType } from 'types/AgentListType';
import Agent from 'components/MetadataItem/Agent';

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
        case 'dateTime':
            const { date, time } = {
                date: value ? value.split(';')[0] : '',
                time: value ? value.split(';')[1] : '',
            };
            return <div>
                {date && moment(date).format('DD-MM-YYYY')} {time}
            </div>;
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
            return <div>
                {value.map((agent: AgentListItemType) => {
                    return <Agent
                        type={agent.type}
                        id={agent.id}
                        name={agent.name}
                    />
                })}
            </div>;
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

    if(value === null || (Array.isArray(value) && value.length === 0)) {
        return null;
    }

    return <MetadataItemContainer label={title} table={table} hideLabel={hideLabel}>
        <MetadataItemValue type={type} dataType={dataType} value={value} />
    </MetadataItemContainer>
}

export default MetadataItem;