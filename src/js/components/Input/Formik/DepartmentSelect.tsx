import React, { Component } from 'react';
import Button from '@mui/material/Button';
import FormItem from 'components/Form/FormItem';
import { FieldProps } from 'formik';
import { OrganizationType } from 'types/OrganizationType';
import FieldErrors from 'components/Input/Formik/Errors';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { Autocomplete, TextField } from '@mui/material';

interface DepartmentSelectProps extends FieldProps, ComponentWithNotifications {
    organization: OrganizationType;
}

interface DepartmentOption {
    value: string;
    label: string;
    data: {
        id: string | null;
        name: string;
        source: string;
    };
    id?: string | null;
    name?: string;
    source?: string;
}

type DepartmentSelectState = {
    options: DepartmentOption[];
    isLoading: boolean;
};

class DepartmentSelect extends Component<DepartmentSelectProps, DepartmentSelectState> {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: false,
            options: [],
        };
    }

    componentDidUpdate(prevProps) {
        const { organization } = this.props;

        if (organization.id !== prevProps.organization.id) {
            this.getDepartments();
        }
    }

    componentDidMount() {
        this.getDepartments();
    }

    getDepartments = () => {
        const { organization, form, field, notifications } = this.props;

        if (organization.id !== null && organization.source === 'database') {
            this.setState({
                isLoading: true,
            });

            apiClient
                .get('/api/agent/organization/' + organization.id + '/department')
                .then(response => {
                    const options = response.data.map(department => {
                        return {
                            value: department.id,
                            label: department.name,
                            data: department,
                        };
                    });

                    this.setState(
                        {
                            options: options,
                            isLoading: false,
                        },
                        () => {
                            if (options.length === 0) {
                                form.setFieldValue(field.name, {
                                    ...defaultData,
                                    source: 'manual',
                                });
                            }
                        },
                    );
                })
                .catch(error => {
                    this.setState({
                        isLoading: false,
                    });

                    if (error.response && typeof error.response.data.error !== 'undefined') {
                        notifications.show(error.response.data.error, { variant: 'error' });
                    } else {
                        notifications.show('An error occurred', { variant: 'error' });
                    }
                });
        } else {
            this.setState(
                {
                    options: [],
                },
                () => {
                    form.setFieldValue(field.name, { ...defaultData, source: '' });
                },
            );
        }
    };

    toggleManual = () => {
        const { form, field } = this.props;
        const value = field.value ? field.value : [defaultData];

        form.setFieldValue(field.name, {
            ...defaultData,
            source: value.source === 'manual' ? '' : 'manual',
            id: null,
        });
    };

    render() {
        const { organization, field, form } = this.props;
        const { options } = this.state;

        const value = field.value ? field.value : [defaultData];
        const disabled = organization.source === '';

        const manual = organization.source === 'manual' || value.source === 'manual';

        return (
            <div>
                {!manual && (
                    <FormItem label="Department">
                        <Autocomplete
                            options={options}
                            loading={this.state.isLoading}
                            disabled={disabled}
                            noOptionsText={options.length === 0 ? "Loading departments..." : "No departments found"}
                            value={value.id ? options.find(option => option.value === value.id) : undefined}
                            onChange={(event, newValue) => {
                                if (newValue) {
                                    form.setFieldValue(field.name, {
                                        ...newValue.data,
                                        source: 'database',
                                    });
                                }
                            }}
                            getOptionLabel={(option) => option.label}
                            isOptionEqualToValue={(option, value) => option.value === value.value}
                            renderInput={(params) => (
                                <TextField
                                    {...params}
                                    fullWidth
                                    placeholder="Select a department"
                                    size="small"
                                />
                            )}
                            disableClearable={true}
                            sx={{ width: 400 }}
                        />

                        <Button variant="text" className="CannotFind" onClick={this.toggleManual} disabled={disabled}>
                            I cannot find my department
                        </Button>
                    </FormItem>
                )}

                {manual && (
                    <>
                        <FormItem label="Department Name">
                            <TextField
                                value={value.name}
                                onChange={event => {
                                    form.setFieldValue(field.name, {
                                        ...value,
                                        name: event.target.value,
                                    });
                                }}
                                disabled={disabled}
                                autoFocus={value.source === 'manual'}
                                sx={{ width: 400 }}
                            />

                            {organization.source !== 'manual' && (
                                <Button variant="text" className="CannotFind" onClick={this.toggleManual}
                                        disabled={disabled}>
                                    Search for a department
                                </Button>
                            )}
                        </FormItem>
                    </>
                )}

                <FieldErrors field={field} />
            </div>
        );
    }
}

const defaultData = {
    id: null,
    name: '',
    source: '',
};

export default withNotifications(DepartmentSelect);