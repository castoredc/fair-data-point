import React from 'react';
import { DistributionGenerationStatus } from '../MetadataItem/EnumMappings';
import { Icon, TextStyle } from '@castoredc/matter';

export default ({ status }) => {
    const message = DistributionGenerationStatus[status];

    let icon = <Icon type="info" />;
    let variation = 'information';

    if (status === 'success') {
        variation = 'success';
        icon = <Icon type="tickCircledSkinny" />;
    } else if (status === 'not_updated') {
        icon = <Icon type="minusSkinny" />;
        variation = 'quiet';
    } else if (status === 'error') {
        variation = 'error';
        icon = <Icon type="errorCircled" />;
    } else if (status === 'partially') {
        variation = 'warning';
        icon = <Icon type="errorCircled" />;
    }

    return (
        <TextStyle variation={variation}>
            {icon} &nbsp;
            <span>{message}</span>
        </TextStyle>
    );
};
