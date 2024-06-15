import React, { Component } from 'react';
import { Button, Heading, Stack } from '@castoredc/matter';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import FormItem from 'components/Form/FormItem';
import { Field, Form, Formik } from 'formik';
import Input from 'components/Input/Formik/Input';
import File from 'components/Input/Formik/File';
import { downloadFile, isNumeric } from '../../../util';
import * as Yup from 'yup';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';

interface ImportExportProps extends AuthorizedRouteComponentProps {
    type: string;
    dataSpecification: any;
    version: string;
    getDataSpecification: (callback) => void;
}

interface ImportExportState {
    isExporting: boolean;
}

export default class ImportExport extends Component<ImportExportProps, ImportExportState> {
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
            const match = contentDisposition.match(/filename\s*=\s*"(.+)"/i);
            const filename = match?.[1];

            this.setState({ isExporting: false });

            downloadFile(response.data, filename);
        });
    };

    parseFile = (value: FileList | null, setFieldValue) => {
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
                            toast.error(<ToastItem type="error" title="Please upload a valid model export." />);
                        }
                    } else {
                        toast.error(<ToastItem type="error" title="Please upload a valid model export." />);
                    }
                };
            }
        }
    };

    import = (values, { setSubmitting }) => {
        const { type, dataSpecification, history, getDataSpecification } = this.props;

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

                toast.success(<ToastItem type="success" title="The model was successfully imported." />, {
                    position: 'top-right',
                });

                getDataSpecification(() => {
                    history.push(`/dashboard/${type}s/${response.data.dataSpecification}/${response.data.version}/modules`);
                });
            })
            .catch(error => {
                setSubmitting(false);

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred while importing the model." />);
                }
            });
    };

    render() {
        const { isExporting } = this.state;
        const { dataSpecification } = this.props;

        const ImportSchema = Yup.object().shape({
            file: Yup.mixed().required('Please upload a file'),
            version: Yup.string()
                .required()
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
                <Stack>
                    <div>
                        <Formik
                            initialValues={{
                                file: null,
                                version: '',
                            }}
                            onSubmit={this.import}
                            validationSchema={ImportSchema}
                        >
                            {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues, setFieldValue }) => {
                                return (
                                    <Form>
                                        <Heading type="Subsection">Import model</Heading>

                                        <Field
                                            component={File}
                                            name="file"
                                            accept="application/json"
                                            onChange={files => this.parseFile(files, setFieldValue)}
                                        />

                                        <FormItem label="New version number">
                                            <Field component={Input} name="version" value={values.version} />
                                        </FormItem>

                                        <Button type="submit" icon="upload" disabled={values.file === null || isSubmitting}>
                                            Import model
                                        </Button>
                                    </Form>
                                );
                            }}
                        </Formik>
                    </div>
                    <div>
                        <Heading type="Subsection">Export model</Heading>

                        <Button onClick={this.export} icon="download" disabled={isExporting}>
                            Export model
                        </Button>
                    </div>
                </Stack>
            </PageBody>
        );
    }
}
