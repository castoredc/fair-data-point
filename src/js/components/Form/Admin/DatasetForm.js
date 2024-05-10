import React, { Component } from 'react';
import { ValidatorForm } from 'react-form-validator-core';

import '../Form.scss';
import { Link } from 'react-router-dom';
import { toast } from 'react-toastify';
import { Button, Icon, Stack } from '@castoredc/matter';
import FormItem from './../FormItem';
import Input from '../../Input';
import RadioGroup from '../../Input/RadioGroup';
import { mergeData } from '../../../util';
import { apiClient } from 'src/js/network';
import ToastItem from 'components/ToastItem';

export default class DatasetForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: props.dataset ? mergeData(defaultData, props.dataset) : defaultData,
            validation: {},
            isSaved: false,
            submitDisabled: false,
            dataset: props.dataset ? props.dataset : null,
            update: !!props.dataset,
            showApiUser: props.dataset ? !props.dataset.hasApiUser : true,
        };
    }

    handleChange = event => {
        const { data } = this.state;
        const newState = {
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            },
        };
        this.setState(newState);
    };

    handleSubmit = event => {
        const { dataset, mainUrl, history } = this.props;
        event.preventDefault();

        const { data } = this.state;

        if (this.form.isFormValid()) {
            this.setState({
                submitDisabled: true,
                isLoading: true,
            });

            apiClient
                .post('/api/dataset/' + dataset.slug, data)
                .then(response => {
                    this.setState({
                        isSaved: true,
                        isLoading: false,
                        submitDisabled: false,
                    });
                    history.push(mainUrl + '/datasets/' + data.slug);
                    toast.success(<ToastItem type="success" title="The dataset details are saved successfully" />, {
                        position: 'top-right',
                    });
                })
                .catch(error => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastItem type="error" title="An error occurred" />);
                    }
                    this.setState({
                        submitDisabled: false,
                        isLoading: false,
                    });
                });
        }

        return false;
    };

    render() {
        const { data, validation, submitDisabled, dataset } = this.state;

        const required = 'This field is required';

        return (
            <ValidatorForm className="FullHeightForm" ref={node => (this.form = node)} onSubmit={this.handleSubmit} method="post">
                <div className="FormContent">
                    {dataset.study && (
                        <FormItem label="Study">
                            <div className="StudyLink">
                                <div className="StudyIcon">
                                    <Icon type="study" width="32px" height="32px" />
                                </div>
                                <div className="StudyDetails">
                                    <div className="StudyName">
                                        <dl>
                                            <dt>Brief name</dt>
                                            <dd>
                                                {dataset.study.hasMetadata ? dataset.study.metadata.briefName : <span className="None">None</span>}
                                            </dd>
                                            <dt>Study name</dt>
                                            <dd>{dataset.study.name}</dd>
                                        </dl>
                                    </div>
                                    <Link to={`/dashboard/studies/${dataset.study.id}`}>
                                        <Button buttonType="secondary">Open study</Button>
                                    </Link>
                                </div>
                            </div>
                        </FormItem>
                    )}

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

                    <FormItem label="Publish dataset">
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
                            value={data.published}
                            variant="horizontal"
                            name="published"
                        />
                    </FormItem>
                </div>

                <div className="FormButtons">
                    <Stack distribution="trailing">
                        <Button disabled={submitDisabled} type="submit">
                            Update dataset
                        </Button>
                    </Stack>
                </div>
            </ValidatorForm>
        );
    }
}

export const defaultData = {
    slug: '',
    published: false,
};
