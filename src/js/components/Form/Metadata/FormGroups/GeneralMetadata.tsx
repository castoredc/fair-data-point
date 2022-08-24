import React, { Component } from 'react';
import FormItem from 'components/Form/FormItem';
// import PublisherModal from "../../../modals/PublisherModal";
import { Field, FormikValues } from 'formik';
import Select from 'components/Input/Formik/Select';
import LocalizedTextInput from 'components/Input/Formik/LocalizedTextInput';

type GeneralMetadataProps = {
    languages: any;
    licenses: any;
    countries: any;
    validation: any;
    values: FormikValues;
    children: React.ReactNode;
};

type GeneralMetadataState = {};

export default class GeneralMetadata extends Component<GeneralMetadataProps, GeneralMetadataState> {
    render() {
        const { validation, languages, licenses, children } = this.props;

        return (
            <div>
                <FormItem label="Title">
                    <Field component={LocalizedTextInput} name="title" languages={languages} serverError={validation} />
                </FormItem>
                <FormItem label="Description">
                    <Field component={LocalizedTextInput} name="description" languages={languages} serverError={validation} multiline rows="8" />
                </FormItem>

                <FormItem label="Language">
                    <Field component={Select} options={languages} name="language" menuPosition="fixed" serverError={validation} />
                </FormItem>

                <FormItem label="License">
                    <Field component={Select} options={licenses} name="license" serverError={validation} />
                </FormItem>

                {children}
            </div>
        );
    }
}
