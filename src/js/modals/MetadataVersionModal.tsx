import React, { Component } from 'react';
import { Button, Modal } from '@castoredc/matter';
import FormItem from 'components/Form/FormItem';
import { Field, Form, Formik } from 'formik';
import Choice from 'components/Input/Formik/Choice';
import Select from 'components/Input/Formik/Select';
import * as Yup from 'yup';
import { apiClient } from '../network';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';

type MetadataVersionModalProps = {
    currentVersion: string;
    defaultMetadataModel: string|null;
    open: boolean;
    onClose: () => void;
    handleSave: (versionType: string) => void;
    metadataModels: any[],
    type: string,
    objectId: string,
};

export default class MetadataVersionModal extends Component<MetadataVersionModalProps> {
    generateVersionNumber = (versionType) => {
        const { currentVersion } = this.props;

        const version = currentVersion ?? '0.0.0';
        const parsedVersion = version.split('.');

        const major = parseInt(parsedVersion[0]);
        const minor = parseInt(parsedVersion[1]);
        const patch = parseInt(parsedVersion[2]);

        let newVersion = '';

        if (versionType === 'major') {
            newVersion = major + 1 + '.' + 0 + '.' + 0;
        } else if (versionType === 'minor') {
            newVersion = major + '.' + (minor + 1) + '.' + 0;
        } else if (versionType === 'patch') {
            newVersion = major + '.' + minor + '.' + (patch + 1);
        }

        return newVersion;
    };


    handleSubmit = (values, { setSubmitting }) => {
        const { objectId, type, handleSave } = this.props;

        apiClient
            .post('/api/metadata/' + type + (type === 'fdp' ? '' : '/' + objectId), values)
            .then(response => {
                setSubmitting(false);

                toast.success(<ToastItem type="success" title="The metadata are saved successfully" />, {
                    position: 'top-right',
                });

                handleSave(values.newVersion);
            })
            .catch(error => {
                toast.error(<ToastItem type="error" title="An error occurred" />);

                setSubmitting(false);
            });
    };

    render() {
        const { open, onClose, handleSave, currentVersion, metadataModels, defaultMetadataModel } = this.props;

        const title = currentVersion ? 'New metadata version' : 'Add metadata';

        return (
            <Modal open={open} title={title} accessibleName={title} onClose={onClose}>
                <Formik initialValues={{
                    versionType: currentVersion ? '' : 'patch',
                    model: defaultMetadataModel ? defaultMetadataModel : '',
                    modelVersion: '',
                    newVersion: currentVersion ? '' : '0.0.1',
                }} onSubmit={this.handleSubmit} validationSchema={VersionSchema}>
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
                        const model = values.model ? metadataModels.find((metadataModel => metadataModel.value === values.model)) : null;
                        const versions = model ? model.versions : [];

                        return (
                            <Form style={{ width: 400 }}>
                                {currentVersion && <FormItem
                                    label="Please indicate to what extent you are going to make changes to the metadata, in order to generate a version number">
                                    <Field
                                        component={Choice}
                                        options={[
                                            { value: 'major', labelText: 'Major changes' },
                                            { value: 'minor', labelText: 'Minor changes' },
                                            { value: 'patch', labelText: 'Patch' },
                                        ]}
                                        onChange={e => {
                                            setFieldValue('versionType', e.target.value);
                                            setFieldValue('newVersion', this.generateVersionNumber(e.target.value));
                                        }}
                                        name="versionType"
                                    />
                                </FormItem>}

                                <FormItem label="Metadata model">
                                    <Field
                                        component={Select}
                                        options={metadataModels}
                                        name="model"
                                        menuPosition="fixed"
                                        menuPlacement="auto"
                                        onChange={(e) => {
                                        setFieldValue('model', e.value);
                                        setFieldValue('modelVersion', '');
                                    }}/>
                                </FormItem>

                                <FormItem label="Metadata model version">
                                    <Field
                                        component={Select}
                                        options={versions}
                                        name="modelVersion"
                                        menuPosition="fixed"
                                        menuPlacement="auto"
                                    />
                                </FormItem>

                                {currentVersion && <FormItem label="Current version">{currentVersion}</FormItem>}

                                {values.newVersion !== '' && <FormItem label="New version">{values.newVersion}</FormItem>}

                                <Button type="submit" disabled={isSubmitting}>
                                    {currentVersion ? 'Create new version' : 'Add metadata'}
                                </Button>
                            </Form>

                        );
                    }}
                </Formik>
            </Modal>
        );
    }
}

const VersionSchema = Yup.object().shape({
    versionType: Yup.string().required('Please select an option'),
    model: Yup.string().required('Please select a metadata model'),
    modelVersion: Yup.string().required('Please select a metadata model version'),
});