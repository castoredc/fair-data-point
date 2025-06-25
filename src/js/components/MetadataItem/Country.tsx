import React from 'react';

import './MetadataItem.scss';
import useGetCountry from '../../hooks/useGetCountry';

interface CountryProps {
    code: string;
}

const Country: React.FC<CountryProps> = ({ code }) => {
    const { country, isLoading } = useGetCountry(code);

    if (isLoading || !country) {
        return <div className="Country">&nbsp;</div>;
    }

    return <div className="Country">{country.label}</div>;
};

export default Country;
