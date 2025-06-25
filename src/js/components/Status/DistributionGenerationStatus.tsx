import React from 'react';
import { DistributionGenerationStatus } from '../MetadataItem/EnumMappings';
import CheckIcon from '@mui/icons-material/Check';
import RemoveIcon from '@mui/icons-material/Remove';
import ErrorIcon from '@mui/icons-material/Error';
import InfoIcon from '@mui/icons-material/Info';

interface StatusProps {
    status: keyof typeof DistributionGenerationStatus;
}

const StatusComponent: React.FC<StatusProps> = ({ status }) => {
    const message = DistributionGenerationStatus[status];

    let icon = <InfoIcon />;
    let variation: 'information' | 'success' | 'quiet' | 'error' | 'warning' = 'information';

    if (status === 'success') {
        variation = 'success';
        icon = <CheckIcon />;
    } else if (status === 'not_updated') {
        icon = <RemoveIcon />;
        variation = 'quiet';
    } else if (status === 'error') {
        variation = 'error';
        icon = <ErrorIcon />;
    } else if (status === 'partially') {
        variation = 'warning';
        icon = <ErrorIcon />;
    }

    return (
        <div>
            {icon} &nbsp;
            <span>{message}</span>
        </div>
    );
};

export default StatusComponent;
