import React, {FC, FormEvent} from 'react'

import {Choice as MatterChoice} from "@castoredc/matter";
import {FieldProps} from "formik";
import FieldErrors from "components/Input/Formik/Errors";
import {ChoiceOptionProps} from "@castoredc/matter/lib/types/src/ChoiceOption/ChoiceOption";

interface ChoiceProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (event: FormEvent<HTMLFieldSetElement>) => void,
    options: ChoiceOptionProps[],
    serverError?: any,
    collapse?: boolean,
    multiple?: boolean,
}

const Choice: FC<ChoiceProps> = ({
                                     field,
                                     readOnly,
                                     onChange,
                                     options,
                                     serverError,
                                     collapse,
                                     multiple
                                 }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return <>
        <MatterChoice
            hideLabel
            labelText={field.name}
            name={field.name}
            options={options}
            collapse={collapse}
            onChange={onChange ? (event) => {
                onChange(event);
                field.onChange(event)
            } : field.onChange}
            value={field.value}
            disabled={readOnly}
            multiple={multiple}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

export default Choice;
