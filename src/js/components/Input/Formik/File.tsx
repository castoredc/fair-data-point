import React, {ChangeEvent, FC} from 'react'

import {FileSelector} from "@castoredc/matter";
import {FieldProps} from "formik";
import FieldErrors from "components/Input/Formik/Errors";
import {ValidationMessageProps} from "@castoredc/matter/lib/types/src/ValidationMessage/ValidationMessage";

interface FileProps extends FieldProps {
    readOnly?: boolean,
    onChange?: (files: FileList | null) => void,
    serverError?: any,
    dropAreaDefaultMessage?: string;
    id?: string;
    fileTypeValidationMessage?: (acceptedFileType: string) => string;
    multipleFilesValidationMessage?: string;
    validationMessageContent?: string;
    validationMessageSize?: ValidationMessageProps['size'];
    validationMessageType?: ValidationMessageProps['type'];
}

const File: FC<FileProps> = ({field, form, meta, readOnly, onChange, serverError, ...rest}) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return <>
        <FileSelector
            name={field.name}
            onChange={onChange ? (event) => {
                const files = event.target.files;
                onChange(files);
                field.onChange({target: {name: field.name, value: files}});
            } : field.onChange}
            onBlur={field.onBlur}
            readOnly={readOnly}
            {...rest}
        />

        <FieldErrors field={field} serverErrors={serverErrors}/>
    </>;
}

export default File;
