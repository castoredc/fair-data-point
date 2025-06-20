import React from 'react';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import Box from '@mui/material/Box';

export const Node = (title: string, type: string, value: any, repeated: boolean) => {
    const nodeInfoStyle = {
        fontSize: 'small',
        color: 'text.secondary',
    };

    return (
        <div>
            {title}
            {type === 'internal' && (
                <Box className="NodeInfo Slug" sx={nodeInfoStyle}>
                    {repeated ? <span>/{value}/[instance_id]</span> : <span>/{value}</span>}
                </Box>
            )}
            {type === 'external' && (
                <Box className="NodeInfo PrefixedUri" sx={nodeInfoStyle}>
                    <span>
                        {value.prefixedValue !== null ? value.prefixedValue : `...:${value.base}`}
                        &nbsp;
                        <OpenInNewIcon sx={{ fontSize: 'inherit' }} />
                    </span>
                </Box>
            )}
            {type === 'value' && (
                <Box className="NodeInfo Value" sx={nodeInfoStyle}>
                    <span>
                        {value.value === 'annotated' ? 'Annotated value' : `Plain value (${value.dataType})`}
                        {repeated && ' - repeated'}
                    </span>
                </Box>
            )}
            {type === 'literal' && (
                <Box className="NodeInfo Literal" sx={nodeInfoStyle}>
                    <span>
                        {value.value} ({value.dataType})
                    </span>
                </Box>
            )}
            {type === 'children' && (
                <Box className="NodeInfo Literal" sx={nodeInfoStyle}>
                    <span>
                        Children of type &nbsp;
                        <OpenInNewIcon sx={{ fontSize: 'inherit' }} />
                    </span>
                </Box>
            )}
            {type === 'parents' && (
                <Box className="NodeInfo Literal" sx={nodeInfoStyle}>
                    <span>
                        Parents of type &nbsp;
                        <OpenInNewIcon sx={{ fontSize: 'inherit' }} />
                    </span>
                </Box>
            )}
        </div>
    );
};
