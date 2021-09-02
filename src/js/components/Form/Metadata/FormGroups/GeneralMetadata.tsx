import React, {Component} from 'react';
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import axios from "axios";
import FormItem from "components/Form/FormItem";
import {Button, CellText, DataGrid, Stack, Tabs} from "@castoredc/matter";
import {mergeData, replaceAt, ucfirst} from "../../../../util";
// import PublisherModal from "../../../modals/PublisherModal";
import {Field, Formik, FormikValues} from "formik";
import Select from "components/Input/Formik/Select";
import LocalizedTextInput from "components/Input/Formik/LocalizedTextInput";

type GeneralMetadataProps = {
    languages: any,
    licenses: any,
    countries: any,
    validation: any,
    values: FormikValues
}

type GeneralMetadataState = {
}

export default class GeneralMetadata extends Component<GeneralMetadataProps, GeneralMetadataState> {
    render() {
        const {
            validation,
            languages,
            licenses,
            values
        } = this.props;

        return <div>
                <FormItem label="Title">
                    <Field
                        component={LocalizedTextInput}
                        name="title"
                        // onChange={this.handleChange}
                        // value={values.title}
                        // serverError={validation.title}
                        languages={languages}
                        serverError={validation}
                    />
                </FormItem>
                <FormItem label="Description">
                    <Field
                        component={LocalizedTextInput}
                        name="description"
                        // onChange={this.handleChange}
                        // value={values.description}
                        // serverError={validation.description}
                        languages={languages}
                        serverError={validation}
                        multiline
                        rows="8"
                    />
                </FormItem>

                <FormItem label="Language">
                    <Field
                        component={Select}
                        options={languages}
                        name="language"
                        menuPosition="fixed"
                        serverError={validation}
                    />
                </FormItem>

                <FormItem label="License">
                    <Field
                        component={Select}
                        options={licenses}
                        name="license"
                        serverError={validation}
                    />
                </FormItem>
            </div>;
    }
}