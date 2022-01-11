import React, {FC} from "react";
import {ErrorMessage, FieldInputProps} from "formik";
import {Space, ValidationMessage} from "@castoredc/matter";

const FieldErrors: FC<{ field: FieldInputProps<any>, serverErrors?: any }> = ({field, serverErrors}) => {
    return <>
        <ErrorMessage
            name={field.name}
            render={msg => {
                if (typeof msg === 'object') {
                    return Object.values(msg).map((message: string) => (
                        <>
                            <Space bottom="default"/>
                            <ValidationMessage type="error">{message}</ValidationMessage>
                        </>
                ));
                } else {
                    return <>
                        <Space bottom="default"/>
                        <ValidationMessage type="error">{msg}</ValidationMessage>
                    </>;
                }
            }}
        />

        {serverErrors && <ValidationMessage type="error">
            {serverErrors.map((errorText, index) => (
                <div key={index}>
                    {errorText}
                </div>
            ))}
        </ValidationMessage>}
    </>;
}

export default FieldErrors;