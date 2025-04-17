import React, { Component } from 'react';
import Button from '@mui/material/Button';
import FormItem from 'components/Form/FormItem';
import { FieldProps } from 'formik';
import debounce from 'lodash/debounce';
import FieldErrors from 'components/Input/Formik/Errors';
import { apiClient } from 'src/js/network';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { TextField } from '@mui/material';

interface OrganizationSelectProps extends FieldProps, ComponentWithNotifications {
    country: string;
    departmentField: string;
}

type OrganizationSelectState = {
    cachedOptions: {
        label: string,
        value: string
    }[];
};

class OrganizationSelect extends Component<OrganizationSelectProps, OrganizationSelectState> {
    constructor(props) {
        super(props);

        this.state = {
            cachedOptions: [],
        };
    }

    loadOrganizations = debounce((input: string, callback: (options) => void) => {
        const { country, notifications } = this.props;

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
                    },
                );
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
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
                <Stack direction="row">
                    {!manual && (
                        <FormItem label="Organization / Institution">
                            // TODO: Replace dropdown
                            {/*<Dropdown*/}
                            {/*    name="organization"*/}
                            {/*    loadOptions={this.loadOrganizations}*/}
                            {/*    options={cachedOptions}*/}

                            {/*    isDisabled={disabled}*/}
                            {/*    onChange={this.handleOrganizationSelect}*/}
                            {/*    components={{ DropdownIndicator: AsyncDropdownIndicator }}*/}
                            {/*    getOptionLabel={({ label }) => label}*/}
                            {/*    getOptionValue={({ value }) => value}*/}
                            {/*    openMenuOnClick={false}*/}
                            {/*    value={{*/}
                            {/*        source: value.source,*/}
                            {/*        value: value.id,*/}
                            {/*        label: value.name,*/}
                            {/*        city: value.city,*/}
                            {/*    }}*/}
                            {/*/>*/}

                            <Button variant="text" className="CannotFind" onClick={this.toggleManual}
                                    disabled={disabled}>
                                I cannot find my organization
                            </Button>
                        </FormItem>
                    )}

                    {manual && (
                        <>
                            <FormItem label="Organization / Institution Name">
                                <TextField
                                    value={value.name}
                                    onChange={event => {
                                        form.setFieldValue(field.name, {
                                            ...value,
                                            name: event.target.value,
                                        });
                                    }}
                                    autoFocus
                                />

                                <Button variant="text" className="CannotFind" onClick={this.toggleManual}
                                        disabled={disabled}>
                                    Search for an organization
                                </Button>
                            </FormItem>
                            <FormItem label="City">
                                <TextField
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

export default withNotifications(OrganizationSelect);