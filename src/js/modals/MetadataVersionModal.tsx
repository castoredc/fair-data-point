import React, { Component } from 'react';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import FormItem from 'components/Form/FormItem';
import { Field, Form, Formik } from 'formik';
import Choice from 'components/Input/Formik/Choice';
import Select from 'components/Input/Formik/Select';
import * as Yup from 'yup';
import { apiClient } from '../network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface MetadataVersionModalProps extends ComponentWithNotifications {
    currentVersion: string;
    defaultMetadataModel: string | null;
    open: boolean;
    onClose: () => void;
    handleSave: (versionType: string) => void;
    metadataModels: any[];
    type: string;
    objectId: string;
};

class MetadataVersionModal extends Component<MetadataVersionModalProps> {
    generateVersionNumber = versionType => {
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
        const { objectId, type, handleSave, notifications } = this.props;

        apiClient
            .post('/api/metadata/' + type + (type === 'fdp' ? '' : '/' + objectId), values)
            .then(response => {
                setSubmitting(false);

                notifications.show('The metadata are saved successfully', {
                    variant: 'success',

                });

                handleSave(values.newVersion);
            })
            .catch(error => {
                notifications.show('An error occurred', { variant: 'error' });

                setSubmitting(false);
            });
    };

    render() {
        const { open, onClose, handleSave, currentVersion, metadataModels, defaultMetadataModel } = this.props;

        const title = currentVersion ? 'New metadata version' : 'Add metadata';

        return (
            <Modal open={open} title={title} onClose={onClose}>
                <Formik
                    initialValues={{
                        versionType: currentVersion ? '' : 'patch',
                        model: defaultMetadataModel ? defaultMetadataModel : '',
                        modelVersion: '',
                        newVersion: currentVersion ? '' : '0.0.1',
                    }}
                    onSubmit={this.handleSubmit}
                    validationSchema={VersionSchema}
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
                        const model = values.model ? metadataModels.find(metadataModel => metadataModel.value === values.model) : null;
                        const versions = model ? model.versions : [];

                        return (
                            <Form style={{ width: 400 }}>
                                {currentVersion && (
                                    <FormItem
                                        label="Please indicate to what extent you are going to make changes to the metadata, in order to generate a version number">
                                        <Field
                                            component={Choice}
                                            options={[
                                                { value: 'major', label: 'Major changes' },
                                                { value: 'minor', label: 'Minor changes' },
                                                { value: 'patch', label: 'Patch' },
                                            ]}
                                            onChange={e => {
                                                setFieldValue('versionType', e.target.value);
                                                setFieldValue('newVersion', this.generateVersionNumber(e.target.value));
                                            }}
                                            name="versionType"
                                        />
                                    </FormItem>
                                )}

                                <FormItem label="Metadata model">
                                    <Field
                                        component={Select}
                                        options={metadataModels}
                                        name="model"


                                        onChange={e => {
                                            setFieldValue('model', e.value);
                                            setFieldValue('modelVersion', '');
                                        }}
                                    />
                                </FormItem>

                                <FormItem label="Metadata model version">
                                    <Field component={Select} options={versions} name="modelVersion" />
                                </FormItem>

                                {currentVersion && <FormItem label="Current version">{currentVersion}</FormItem>}

                                {values.newVersion !== '' &&
                                    <FormItem label="New version">{values.newVersion}</FormItem>}

                                <Button
                                    type="submit"
                                    disabled={isSubmitting}
                                    variant="contained"
                                >
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

export default withNotifications(MetadataVersionModal);