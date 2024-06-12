import React, { Component } from 'react';

import './MetadataItem.scss';
import useGetLicense from '../../hooks/useGetLicense';

interface LicenseProps {
    slug: string;
}

const License: React.FC<LicenseProps> = ({slug}) => {
    const { license, isLoading } = useGetLicense(slug);

    if (isLoading || !license) {
        return <div className="License">&nbsp;</div>;
    }

    return <div className="License">{license.label}</div>;
}

export default License;
