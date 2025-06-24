import React, { Component } from 'react';
import Button from '@mui/material/Button';
import FormItem from 'components/Form/FormItem';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import File from 'components/Input/Formik/File';
import { downloadFile, isNumeric } from '../../../util';
import * as Yup from 'yup';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import Stack from '@mui/material/Stack';
import DownloadIcon from '@mui/icons-material/Download';
import UploadIcon from '@mui/icons-material/Upload';
import { Typography } from '@mui/material';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface ImportExportProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
    dataSpecification: any;
    version: string;
    getDataSpecification: (callback) => void;
}

interface ImportExportState {
    isExporting: boolean;
}

class ImportExport extends Component<ImportExportProps, ImportExportState> {
    constructor(props) {
        super(props);

        this.state = {
            isExporting: false,
        };
    }

    export = () => {
        const { type, dataSpecification, version } = this.props;

        this.setState({ isExporting: true });

        apiClient({
            url: '/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/export',
            method: 'GET',
            responseType: 'blob',
        }).then(response => {
            const contentDisposition = response.headers['content-disposition'];
            const match = contentDisposition ? contentDisposition.match(/filename\s*=\s*"(.+)"/i) : null;
            const filename = match?.[1];

            this.setState({ isExporting: false });

            downloadFile(response.data, filename);
        });
    };

    parseFile = (value: FileList | null, setFieldValue) => {
        const { notifications } = this.props;

        if (value !== null && value.length > 0) {
            const file = value.item(0);

            if (file !== null && file.type === 'application/json') {
                let reader = new FileReader();
                reader.readAsText(file);

                reader.onload = () => {
                    const result = reader.result;

                    if (result !== null) {
                        const json = JSON.parse(result.toString());

                        if ('model' in json) {
                            setFieldValue('version', json.version.version);
                        } else {
                            notifications.show('Please upload a valid model export.', { variant: 'error' });
                        }
                    } else {
                        notifications.show('Please upload a valid model export.', { variant: 'error' });
                    }
                };
            }
        }
    };

    import = (values, { setSubmitting }) => {
        const { type, dataSpecification, history, getDataSpecification, notifications } = this.props;

        const formData = new FormData();

        formData.append('file', values.file.item(0));
        formData.append('version', values.version);

        apiClient
            .post('/api/' + type + '/' + dataSpecification.id + '/import', formData, {
                headers: {
                    'content-type': 'multipart/form-data',
                },
            })
            .then(response => {
                setSubmitting(false);

                notifications.show('The model was successfully imported.', {
                    variant: 'success',

                });

                getDataSpecification(() => {
                    history.push(`/dashboard/${type}s/${response.data.dataSpecification}/${response.data.version}/modules`);
                });
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred while importing the model.', { variant: 'error' });
                }
            });
    };

    render() {
        const { isExporting } = this.state;
        const { dataSpecification } = this.props;

        const ImportSchema = Yup.object().shape({
            file: Yup.mixed().required('Please upload a file'),
            version: Yup.string()
                .required('Please enter a valid version number (X.X.X)')
                .test('isValidVersion', 'Please enter a valid version number (X.X.X)', value => {
                    if (value === undefined) {
                        return false;
                    }
                    const parsedVersion = value.split('.');

                    if (parsedVersion.length !== 3) {
                        return false;
                    }

                    return isNumeric(parsedVersion[0]) && isNumeric(parsedVersion[1]) && isNumeric(parsedVersion[2]);
                })
                .test('isNonExistentVersion', 'This version already exists', value => {
                    return dataSpecification.versions.find(({ version }) => version === value) === undefined;
                }),
        });

        return (
            <PageBody>
                <Stack direction="row">
                    <div>
                        <Formik
                            initialValues={{
                                file: null,
                                version: '',
                            }}
                            onSubmit={this.import}
                            validationSchema={ImportSchema}
                        >
                            {({
                                  values,
                                  errors,
                                  touched,
                                  handleChange,
                                  handleBlur,
                                  handleSubmit,
                                  isSubmitting,
                                  setValues,
                                  setFieldValue,
                              }) => {
                                return (
                                    <Form>
                                        <Typography variant="h4" sx={{ mb: 2 }}>
                                            Import model
                                        </Typography>

                                        <Field
                                            component={File}
                                            name="file"
                                            accept="application/json"
                                            onChange={files => this.parseFile(files, setFieldValue)}
                                        />

                                        <FormItem label="New version number">
                                            <Field component={Input} name="version" value={values.version} />
                                        </FormItem>

                                        <Button
                                            type="submit" startIcon={<UploadIcon />}
                                            disabled={values.file === null || isSubmitting}
                                        >
                                            Import model
                                        </Button>
                                    </Form>
                                );
                            }}
                        </Formik>
                    </div>
                    <div>
                        <Typography variant="h4" sx={{ mb: 2 }}>
                            Export model
                        </Typography>

                        <Button onClick={this.export} variant="outlined" startIcon={<DownloadIcon />} disabled={isExporting}>
                            Export model
                        </Button>
                    </div>
                </Stack>
            </PageBody>
        );
    }
}

export default withNotifications(ImportExport);