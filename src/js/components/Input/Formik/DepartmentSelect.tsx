import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastContent from 'components/ToastContent';
import FormItem from 'components/Form/FormItem';
import { Button, Dropdown, TextInput } from '@castoredc/matter';
import { FieldProps } from 'formik';
import { OptionType } from 'components/Input/Formik/Select';
import { ActionMeta } from 'react-select/src/types';
import { OrganizationType } from 'types/OrganizationType';
import FieldErrors from 'components/Input/Formik/Errors';
import { apiClient } from 'src/js/network';

interface DepartmentSelectProps extends FieldProps {
    organization: OrganizationType;
}

type DepartmentSelectState = {
    options: any;
    isLoading: boolean;
};

export default class DepartmentSelect extends Component<DepartmentSelectProps, DepartmentSelectState> {
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
        const { organization, form, field } = this.props;

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
                        }
                    );
                })
                .catch(error => {
                    this.setState({
                        isLoading: false,
                    });

                    if (error.response && typeof error.response.data.error !== 'undefined') {
                        toast.error(<ToastContent type="error" message={error.response.data.error} />);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred" />);
                    }
                });
        } else {
            this.setState(
                {
                    options: [],
                },
                () => {
                    form.setFieldValue(field.name, { ...defaultData, source: '' });
                }
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
                        <Dropdown
                            name="organization"
                            options={options}
                            menuPosition="fixed"
                            isDisabled={disabled}
                            onChange={(value: OptionType, action: ActionMeta<OptionType>) => {
                                const department = options.find((option: OptionType) => value.value === option.value);
                                form.setFieldValue(field.name, {
                                    ...department.data,
                                    source: 'database',
                                });
                            }}
                            getOptionLabel={({ label }) => label}
                            getOptionValue={({ value }) => value}
                            value={value.id && options.find((option: OptionType) => value.id === option.value)}
                        />

                        <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual} disabled={disabled}>
                            I cannot find my department
                        </Button>
                    </FormItem>
                )}

                {manual && (
                    <>
                        <FormItem label="Department Name">
                            <TextInput
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
                                <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual} disabled={disabled}>
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
