import React, {FC, FormEvent} from 'react'

import {Choice as MatterChoice, ChoiceOption} from "@castoredc/matter";
import {FieldProps} from "formik";
import FieldErrors from "components/Input/Formik/Errors";
import {ChoiceOptionProps} from "@castoredc/matter/lib/types/src/ChoiceOption/ChoiceOption";

interface SingleChoiceProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (event: FormEvent<HTMLInputElement>) => void,
    serverError?: any,
    labelText: string,
}

const SingleChoice: FC<SingleChoiceProps> = ({
                                     field,
                                     readOnly,
                                     onChange,
                                     serverError,
                                                 labelText
                                 }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return <>
        <ChoiceOption
            checked={field.value}
            labelText={labelText}
            name={field.name}
            type="checkbox"
            onChange={onChange ? (event) => {
                onChange(event);
                field.onChange(event)
            } : field.onChange}
            disabled={readOnly}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

export default SingleChoice;
