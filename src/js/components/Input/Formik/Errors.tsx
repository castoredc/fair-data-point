import React, { FC, Fragment } from 'react';
import { ErrorMessage, FieldInputProps } from 'formik';
import { Space, ValidationMessage } from '@castoredc/matter';
import { isUndefined } from 'lodash';

const FieldErrors: FC<{ field: FieldInputProps<any>; serverErrors?: any; index?: number }> = ({ field, serverErrors, index }) => {
    return (
        <>
            {/* @ts-ignore */}
            <ErrorMessage
                name={!isUndefined(index) ? `${field.name}[${index}]` : field.name}
                render={msg => {
                    if (typeof msg === 'object') {
                        return Object.values(msg).map((message: string, index: number) => (
                            <Fragment key={index}>
                                {isUndefined(index) && <Space bottom="default" />}
                                <ValidationMessage type="error">{message}</ValidationMessage>
                                {!isUndefined(index) && <Space bottom="default" />}
                            </Fragment>
                        ));
                    } else {
                        return (
                            <>
                                <Space bottom="default" />
                                <ValidationMessage type="error">{msg}</ValidationMessage>
                            </>
                        );
                    }
                }}
            />

            {serverErrors && (
                <ValidationMessage type="error">
                    {serverErrors.map((errorText, index) => (
                        <div key={index}>{errorText}</div>
                    ))}
                </ValidationMessage>
            )}
        </>
    );
};

export default FieldErrors;
