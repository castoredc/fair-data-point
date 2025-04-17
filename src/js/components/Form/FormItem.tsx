import React, { FC } from 'react';
import { classNames } from '../../util';
import Tooltip from '@mui/material/Tooltip';
import { Box, FormLabel } from '@mui/material';
import InfoIcon from '@mui/icons-material/Info';
import Typography from '@mui/material/Typography';

interface FormItemProps {
    label?: string;
    children: React.ReactNode;
    hidden?: boolean;
    inline?: boolean;
    align?: string;
    className?: string;
    tooltip?: string;
    details?: string;
    isRequired?: boolean;
}

const FormItem: FC<FormItemProps> = ({
                                         label,
                                         children,
                                         hidden,
                                         inline,
                                         align,
                                         className,
                                         tooltip,
                                         details,
                                         isRequired,
                                     }) => {
    let alignClass = '';

    if (align === 'left') {
        alignClass = 'AlignLeft';
    } else if (align === 'center') {
        alignClass = 'AlignCenter';
    } else if (align === 'right') {
        alignClass = 'AlignRight';
    }

    return (
        <Box sx={{ mb: 2 }}>
            {label && (
                <div className="FormItemLabel">
                    <FormLabel>
                        {label}
                        {isRequired && (
                            <Tooltip title="This field is required">
                                <span className="RequiredIndicator">*</span>
                            </Tooltip>
                        )}
                        {tooltip && (
                            <>
                                &nbsp;
                                <Tooltip title={tooltip}>
                                    <InfoIcon />
                                </Tooltip>
                            </>
                        )}
                    </FormLabel>
                    {details && <div>
                        <Typography variant="caption">{details}</Typography>
                    </div>}
                </div>
            )}
            <div className="FormItemContent">
                {children}
            </div>
        </Box>
    );
};

export default FormItem;
