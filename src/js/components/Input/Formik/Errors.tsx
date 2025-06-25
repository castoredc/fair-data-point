import React, { FC, Fragment } from 'react';
import { ErrorMessage, FieldInputProps } from 'formik';
import { isUndefined } from 'lodash';
import { FormHelperText } from '@mui/material';

const FieldErrors: FC<{ field: FieldInputProps<any>; serverErrors?: any; index?: number }> = ({
                                                                                                  field,
                                                                                                  serverErrors,
                                                                                                  index,
                                                                                              }) => {
    return (
        <>
            {/* @ts-ignore */}
            <ErrorMessage
                name={!isUndefined(index) ? `${field.name}[${index}]` : field.name}
                render={msg => {
                    if (typeof msg === 'object') {
                        return Object.values(msg).map((message: string, index: number) => (
                            <Fragment key={index}>
                                <FormHelperText error={true}>{message}</FormHelperText>
                            </Fragment>
                        ));
                    } else {
                        return (
                            <>
                                <FormHelperText error={true}>{msg}</FormHelperText>
                            </>
                        );
                    }
                }}
            />

            {serverErrors && (
                <FormHelperText error={true}>
                    {serverErrors.map((errorText, index) => (
                        <div key={index}>{errorText}</div>
                    ))}
                </FormHelperText>
            )}
        </>
    );
};

export default FieldErrors;
