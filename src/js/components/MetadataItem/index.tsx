import React from 'react';
import { localizedText } from '../../util';
import MetadataItemContainer from 'components/MetadataItem/MetadataItemContainer';
import Language from 'components/MetadataItem/Language';
import License from 'components/MetadataItem/License';
import OntologyConcept from 'components/MetadataItem/OntologyConcept';
import Country from 'components/MetadataItem/Country';
import { OntologyConceptType } from 'types/OntologyConceptType';
import moment from 'moment';
import { AgentListItemType } from 'types/AgentListType';
import Agent from 'components/MetadataItem/Agent';
import { Box, Link, Stack } from '@mui/material';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';

interface MetadataItemProps {
    title: string;
    type: string;
    dataType: string;
    value: any;
    table?: boolean;
    hideLabel?: boolean;
}

interface MetadataItemValueProps {
    type: string;
    dataType: string;
    value: any;
}

const MetadataItemValue: React.FC<MetadataItemValueProps> = ({ type, dataType, value }) => {
    switch (type) {
        case 'heading':
            return <Box>{dataType === 'langString' ? localizedText(value, 'en') : value}</Box>;
        case 'description':
            return <Box>{dataType === 'langString' ? localizedText(value, 'en') : value}</Box>;
        case 'paragraph':
            return <Box>{dataType === 'langString' ? localizedText(value, 'en') : value}</Box>;
        case 'ontologyConcepts':
            return (
                <Stack direction="row" spacing={1} flexWrap="wrap">
                    {value.map((ontologyConcept: OntologyConceptType) => (
                        <OntologyConcept
                            key={ontologyConcept.code}
                            code={ontologyConcept.code}
                            displayName={ontologyConcept.displayName}
                            ontology={ontologyConcept.ontology}
                            url={ontologyConcept.url}
                        />
                    ))}
                </Stack>
            );
        case 'date':
            return <Box>{value}</Box>;
        case 'time':
            return <Box>{value}</Box>;
        case 'dateTime':
            const { date, time } = {
                date: value ? value.split(';')[0] : '',
                time: value ? value.split(';')[1] : '',
            };
            return (
                <Box>
                    {date && moment(date).format('DD-MM-YYYY')} {time}
                </Box>
            );
        case 'yesNo':
            return <Box>{value}</Box>;
        case 'list':
            return (
                <Stack spacing={1}>
                    {value.map((item, index) => (
                        <Box key={index}>{item}</Box>
                    ))}
                </Stack>
            );
        case 'language':
            return <Language code={value} />;
        case 'license':
            return <License slug={value} />;
        case 'country':
            return <Country code={value} />;
        case 'agents':
            return (
                <Stack direction="row" spacing={1} flexWrap="wrap">
                    {value.map((agent: AgentListItemType) => (
                        <Agent key={agent.id} type={agent.type} id={agent.id} name={agent.name} />
                    ))}
                </Stack>
            );
        case 'image':
            return (
                <Box
                    component="img"
                    src={value}
                    sx={{
                        maxWidth: '100%',
                        height: 'auto',
                        borderRadius: 1,
                    }}
                />
            );
        case 'link':
            return (
                <Stack
                    direction="row"
                    spacing={0.5}
                    alignItems="center"
                >
                    <Link
                        href={value}
                        target="_blank"
                        sx={{
                            color: 'text.secondary',
                            textDecoration: 'none',
                            '&:hover': {
                                textDecoration: 'underline',
                            },
                        }}
                    >
                        {value}
                    </Link>
                    <OpenInNewIcon sx={{ fontSize: '0.875rem', color: 'text.secondary' }} />
                </Stack>
            );
    }

    return null;
};

const MetadataItem: React.FC<MetadataItemProps> = ({ title, type, dataType, value, table, hideLabel }) => {
    hideLabel = ['image'].includes(type) || hideLabel;

    if (value === null || (Array.isArray(value) && value.length === 0)) {
        return null;
    }

    return (
        <MetadataItemContainer label={title} table={table} hideLabel={hideLabel}>
            <MetadataItemValue type={type} dataType={dataType} value={value} />
        </MetadataItemContainer>
    );
};

export default MetadataItem;
