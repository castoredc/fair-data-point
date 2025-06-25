import React, { FC } from 'react';
import { FieldProps } from 'formik';
import FieldErrors from 'components/Input/Formik/Errors';
import Button from '@mui/material/Button';
import CloudUploadIcon from '@mui/icons-material/CloudUpload';
import { VisuallyHiddenInput } from 'components/Input/VisuallyHiddenInput';

interface FileProps extends FieldProps {
    readOnly?: boolean;
    onChange?: (files: FileList | null) => void;
    serverError?: any;
    dropAreaDefaultMessage?: string;
    id?: string;
    fileTypeValidationMessage?: (acceptedFileType: string) => string;
    multipleFilesValidationMessage?: string;
    validationMessageContent?: string;
}

const File: FC<FileProps> = ({ field, form, meta, readOnly, onChange, serverError, ...rest }) => {
    const serverErrors = serverError ? serverError[field.name] : undefined;

    return (
        <>
            <Button
                component="label"
                role={undefined}
                variant="contained"
                tabIndex={-1}
                startIcon={<CloudUploadIcon />}
            >
                Upload file

                <VisuallyHiddenInput
                    type="file"
                    multiple
                    name={field.name}
                    onChange={
                        onChange
                            ? event => {
                                const files = event.target.files;
                                onChange(files);
                                field.onChange({ target: { name: field.name, value: files } });
                            }
                            : field.onChange
                    }
                    onBlur={field.onBlur}
                    readOnly={readOnly}
                />
            </Button>

            <FieldErrors field={field} serverErrors={serverErrors} />
        </>
    );
};

export default File;
