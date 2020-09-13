import React, {Component} from 'react';
import {ValidatorForm} from 'react-form-validator-core';

import '../Form.scss'
import {Redirect} from "react-router-dom";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import axios from "axios";
import FormItem from "./../FormItem";
import Input from "../../Input";
import RadioGroup from "../../Input/RadioGroup";
import {mergeData} from "../../../util";
import {Button, Stack} from "@castoredc/matter";

export default class CatalogForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data:           props.catalog ? mergeData(defaultData, props.catalog) : defaultData,
            validation:     {},
            isSaved:        false,
            submitDisabled: false,
            catalog:        props.catalog ? props.catalog : null,
            update:         !!props.catalog,
        };
    }

    handleChange = (event) => {
        const {data} = this.state;
        const newState = {
            data:       {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            },
        };
        this.setState(newState);
    };

    handleSubmit = (event) => {
        const {catalog} = this.props;
        event.preventDefault();

        const {data} = this.state;

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading:      true,
            });

            axios.post('/api/catalog' + (catalog ? '/' + catalog.slug : ''), data)
                .then((response) => {
                    this.setState({
                        isSaved:        true,
                        isLoading:      false,
                        submitDisabled: false,
                        catalog:        response.data,
                    });

                    if (catalog) {
                        toast.success(<ToastContent type="success"
                                                    message="The catalog details are saved successfully"/>, {
                            position: "top-right",
                        });
                    }
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>, {
                            position: "top-center",
                        });
                    }
                    this.setState({
                        submitDisabled: false,
                        isLoading:      false,
                    });
                });
        }

        return false;
    };

    render() {
        const {data, validation, catalog, submitDisabled, update, isSaved} = this.state;

        const required = "This field is required";

        if (isSaved && !update) {
            return <Redirect push to={'/admin/catalog/' + catalog.slug + '/metadata'}/>;
        }

        return (
            <ValidatorForm
                className="FullHeightForm"
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <div className="FormContent">
                    <FormItem label="Slug">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="slug"
                            onChange={this.handleChange}
                            value={data.slug}
                            serverError={validation.slug}
                        />
                    </FormItem>

                    <FormItem label="Accepts submissions">
                        <RadioGroup
                            validators={['required']}
                            errorMessages={[required]}
                            options={[
                                {
                                    label: 'Yes',
                                    value: true,
                                },
                                {
                                    label: 'No',
                                    value: false,
                                },
                            ]}
                            onChange={this.handleChange}
                            value={data.acceptSubmissions}
                            variant="horizontal"
                            name="acceptSubmissions"
                        />
                    </FormItem>

                    {data.acceptSubmissions && <FormItem label="Data will be accessed during submission">
                        <RadioGroup
                            validators={['required']}
                            errorMessages={[required]}
                            options={[
                                {
                                    label: 'Yes',
                                    value: true,
                                },
                                {
                                    label: 'No',
                                    value: false,
                                },
                            ]}
                            onChange={this.handleChange}
                            value={data.submissionAccessesData}
                            variant="horizontal"
                            name="submissionAccessesData"
                        />
                    </FormItem>}
                </div>

                {catalog ? <div className="FormButtons">
                    <Stack distribution="trailing">
                        <Button disabled={submitDisabled} type="submit">
                            Update catalog
                        </Button>
                    </Stack>
                </div> : <footer>
                    <Button disabled={submitDisabled} type="submit">
                        Add catalog
                    </Button>
                </footer>}
            </ValidatorForm>
        );
    }
}

export const defaultData = {
    slug:                   '',
    acceptSubmissions:      '',
    submissionAccessesData: '',
};
