import React, { Component } from 'react';
import Button from '@mui/material/Button';
import FormItem from 'components/Form/FormItem';
import { FieldProps } from 'formik';
import debounce from 'lodash/debounce';
import FieldErrors from 'components/Input/Formik/Errors';
import { apiClient } from 'src/js/network';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { Autocomplete, CircularProgress, TextField } from '@mui/material';

interface OrganizationSelectProps extends FieldProps, ComponentWithNotifications {
    country: string;
    departmentField: string;
}

interface OrganizationOption {
    label: string;
    value: string;
    source: string;
    data: {
        city: string;
    };
}

type OrganizationSelectState = {
    cachedOptions: OrganizationOption[];
    loading: boolean;
    inputValue: string;
};

class OrganizationSelect extends Component<OrganizationSelectProps, OrganizationSelectState> {
    constructor(props) {
        super(props);

        this.state = {
            cachedOptions: [],
            loading: false,
            inputValue: '',
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
                            <Autocomplete
                                options={cachedOptions}
                                loading={this.state.loading}
                                disabled={disabled}
                                noOptionsText={this.state.inputValue ? "No results found" : "Type to search"}
                                value={value.id ? {
                                    label: value.name,
                                    value: value.id,
                                    source: value.source,
                                    data: { city: value.city }
                                } : undefined}
                                onChange={(event, newValue) => {
                                    if (newValue) {
                                        this.handleOrganizationSelect(newValue);
                                    }
                                }}
                                onInputChange={(event, newInputValue) => {
                                    this.setState({ inputValue: newInputValue });
                                    if (newInputValue) {
                                        this.setState({ loading: true });
                                        this.loadOrganizations(newInputValue, (options) => {
                                            this.setState({ loading: false });
                                        });
                                    }
                                }}
                                getOptionLabel={(option) => option.label}
                                isOptionEqualToValue={(option, value) => option.value === value.value}
                                renderInput={(params) => (
                                    <TextField
                                        {...params}
                                        fullWidth
                                        placeholder="Search for an organization"
                                        InputProps={{
                                            ...params.InputProps,
                                            endAdornment: (
                                                <React.Fragment>
                                                    {this.state.loading ? <CircularProgress color="inherit" size={20} /> : null}
                                                    {params.InputProps.endAdornment}
                                                </React.Fragment>
                                            ),
                                        }}
                                        size="small"
                                    />
                                )}
                                disableClearable={true}
                                sx={{ width: 400 }}
                            />

                            <Button variant="text" className="CannotFind" onClick={this.toggleManual}
                                    disabled={disabled}>
                                I cannot find my organization
                            </Button>
                        </FormItem>
                    )}

                    {manual && (
                        <Stack direction="column" spacing={2}>
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
                                    sx={{ width: 400 }}
                                />
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
                                    sx={{ width: 400 }}
                                />
                            </FormItem>

                            <Button variant="text" className="CannotFind" onClick={this.toggleManual}
                                    disabled={disabled}>
                                Search for an organization
                            </Button>
                        </Stack>
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