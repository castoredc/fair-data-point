import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import FormItem from 'components/Form/FormItem';
import { Button, Dropdown, Stack, TextInput } from '@castoredc/matter';
import { FieldProps } from 'formik';
import { AsyncDropdownIndicator, OptionType } from 'components/Input/Formik/Select';
import debounce from 'lodash/debounce';
import { OptionsType } from 'react-select/src/types';
import FieldErrors from 'components/Input/Formik/Errors';
import { apiClient } from 'src/js/network';

interface OrganizationSelectProps extends FieldProps {
    country: string;
    departmentField: string;
}

type OrganizationSelectState = {
    cachedOptions: OptionsType<OptionType>;
};

export default class OrganizationSelect extends Component<OrganizationSelectProps, OrganizationSelectState> {
    constructor(props) {
        super(props);

        this.state = {
            cachedOptions: [],
        };
    }

    loadOrganizations = debounce((input: string, callback: (options) => void) => {
        const { country } = this.props;

        apiClient
            .get('/api/agent/organization', {
                params: {
                    country: country,
                    search: input,
                },
            })
            .then(response => {
                this.setState(
                    {
                        cachedOptions: response.data,
                    },
                    () => {
                        callback(response.data);
                    }
                );
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }

                callback(null);
            });
    }, 300);

    handleOrganizationSelect = organization => {
        const { form, field } = this.props;

        form.setFieldValue(field.name, {
            source: organization.source,
            id: organization.value,
            name: organization.label,
            city: organization.data.city,
        });
    };

    toggleManual = () => {
        const { form, field, departmentField } = this.props;
        const value = field.value ? field.value : [defaultData];

        form.setFieldValue(field.name, {
            ...defaultData,
            source: value.source === 'manual' ? '' : 'manual',
            id: null,
        });

        form.setFieldValue(departmentField, {
            id: null,
            name: '',
            source: value.source === 'manual' ? '' : 'manual',
        });
    };

    render() {
        const { country, field, form } = this.props;
        const { cachedOptions } = this.state;

        const value = field.value ? field.value : [defaultData];
        const disabled = country === '' || country === null;

        const manual = value.source === 'manual';

        return (
            <div>
                <Stack>
                    {!manual && (
                        <FormItem label="Organization / Institution">
                            <Dropdown
                                name="organization"
                                loadOptions={this.loadOrganizations}
                                options={cachedOptions}
                                menuPosition="fixed"
                                isDisabled={disabled}
                                onChange={this.handleOrganizationSelect}
                                components={{ DropdownIndicator: AsyncDropdownIndicator }}
                                getOptionLabel={({ label }) => label}
                                getOptionValue={({ value }) => value}
                                openMenuOnClick={false}
                                value={{
                                    source: value.source,
                                    value: value.id,
                                    label: value.name,
                                    city: value.city,
                                }}
                            />

                            <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual} disabled={disabled}>
                                I cannot find my organization
                            </Button>
                        </FormItem>
                    )}

                    {manual && (
                        <>
                            <FormItem label="Organization / Institution Name">
                                <TextInput
                                    value={value.name}
                                    onChange={event => {
                                        form.setFieldValue(field.name, {
                                            ...value,
                                            name: event.target.value,
                                        });
                                    }}
                                    autoFocus
                                />

                                <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual} disabled={disabled}>
                                    Search for an organization
                                </Button>
                            </FormItem>
                            <FormItem label="City">
                                <TextInput
                                    value={value.city}
                                    onChange={event => {
                                        form.setFieldValue(field.name, {
                                            ...value,
                                            city: event.target.value,
                                        });
                                    }}
                                />
                            </FormItem>
                        </>
                    )}
                </Stack>

                <FieldErrors field={field} />
            </div>
        );
    }
}

const defaultData = {
    id: null,
    name: '',
    source: '',
    city: '',
};
