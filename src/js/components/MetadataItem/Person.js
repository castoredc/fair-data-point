import React from 'react';
import CustomIcon from '../Icon/CustomIcon';

const Person = ({ person }) => {
    const { name, orcid, slug } = person;
    if (orcid) {
        return (
            <a href={`https://orcid.org/${orcid}`} className="Orcid" target="_blank">
                {name}&nbsp;
                <CustomIcon type="orcid" />
            </a>
        );
    } else {
        return <span className="Publisher">{name}</span>;
    }
};

export default Person;
