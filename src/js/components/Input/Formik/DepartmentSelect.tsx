import React, { Component } from 'react';
import Button from '@mui/material/Button';
import FormItem from 'components/Form/FormItem';
import { FieldProps } from 'formik';
import { OrganizationType } from 'types/OrganizationType';
import FieldErrors from 'components/Input/Formik/Errors';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { TextField } from '@mui/material';

interface DepartmentSelectProps extends FieldProps, ComponentWithNotifications {
    organization: OrganizationType;
}

type DepartmentSelectState = {
    options: any;
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
                        // TODO: Replace dropdown
                        {/*<Dropdown*/}
                        {/*    name="organization"*/}
                        {/*    options={options}*/}

                        {/*    isDisabled={disabled}*/}
                        {/*    onChange={(*/}
                        {/*        value: ReactSelectTypes.OnChangeValue<DefaultOptionType, false>,*/}
                        {/*        action: ReactSelectTypes.ActionMeta<DefaultOptionType>,*/}
                        {/*    ) => {*/}
                        {/*        const department = value && options.find((option: DefaultOptionType) => value.value === option.value);*/}
                        {/*        form.setFieldValue(field.name, {*/}
                        {/*            ...department.data,*/}
                        {/*            source: 'database',*/}
                        {/*        });*/}
                        {/*    }}*/}
                        {/*    getOptionLabel={({ label }) => label}*/}
                        {/*    getOptionValue={({ value }) => value}*/}
                        {/*    value={value.id && options.find((option: DefaultOptionType) => value.id === option.value)}*/}
                        {/*/>*/}

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