import React, { Component } from 'react';

import Button from '@mui/material/Button';
import LoadingOverlay from 'components/LoadingOverlay';
import { FieldArray, Form, Formik } from 'formik';
import AffiliationForm from 'components/Form/Agent/AffiliationForm';
import * as Yup from 'yup';
import { UserType } from 'types/UserType';
import { apiClient } from 'src/js/network';
import AddIcon from '@mui/icons-material/Add';
import Stack from '@mui/material/Stack';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface AffiliationsFormProps extends ComponentWithNotifications {
    user: UserType;
    onSaved: () => void;
}

interface AffiliationsFormState {
    isLoading: boolean;
    countries: any;
    validation: any;
    affiliations: any;
}

class AffiliationsForm extends Component<AffiliationsFormProps, AffiliationsFormState> {
    constructor(props) {
        super(props);

        this.state = {
            affiliations: props.user.details.affiliations ?? [],
            countries: null,
            validation: [defaultValidation],
            isLoading: true,
        };
    }

    getCountries = () => {
        const { notifications } = this.props;

        apiClient
            .get('/api/countries')
            .then(response => {
                this.setState({
                    countries: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    componentDidMount() {
        this.getCountries();
    }

    handleSubmit = values => {
        const { onSaved, notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .post('/api/user/affiliations', values.affiliations)
            .then(() => {
                this.setState(
                    {
                        isLoading: false,
                    },
                    () => {
                        onSaved();
                    },
                );
            })
            .catch(error => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    notifications.show('An error occurred while updating your affiliations', { variant: 'error' });
                }
                this.setState({
                    isLoading: false,
                });
            });
    };

    render() {
        const { affiliations, validation, countries, isLoading } = this.state;

        if (isLoading && countries == null) {
            return <LoadingOverlay accessibleLabel="Loading" />;
        }

        const initialValues = {
            affiliations: [defaultData],
        };

        return (
            <>
                <Formik initialValues={initialValues} onSubmit={this.handleSubmit} validationSchema={AffiliationSchema}>
                    {({
                          values,
                          errors,
                          touched,
                          handleChange,
                          handleBlur,
                          handleSubmit,
                          isSubmitting,
                          setValues,
                          setFieldValue,
                      }) => {
                        return (
                            <Form>
                                {isLoading && <LoadingOverlay accessibleLabel="Submitting affiliations" />}
                                <div>
                                    {/* @ts-ignore */}
                                    <FieldArray
                                        name="affiliations"
                                        render={arrayHelpers => (
                                            <>
                                                <div className="Affiliations">
                                                    {values.affiliations.map((affiliation, index) => (
                                                        <AffiliationForm
                                                            values={affiliation}
                                                            index={index}
                                                            key={index}
                                                            validation={validation[index]}
                                                            countries={countries}
                                                            name="affiliations"
                                                            handleRemove={() => arrayHelpers.remove(index)}
                                                            setFieldValue={setFieldValue}
                                                        />
                                                    ))}
                                                </div>
                                                <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                                                    <Button
                                                        variant="outlined"
                                                        startIcon={<AddIcon />}
                                                        onClick={() => arrayHelpers.push(defaultData)}
                                                    >
                                                        Add another affiliation
                                                    </Button>
                                                </Stack>
                                            </>
                                        )}
                                    />
                                </div>

                                <div>
                                    <Stack direction="row" sx={{ justifyContent: 'space-between' }}>
                                        <Button type="submit" disabled={isLoading} variant="contained">
                                            Save details
                                        </Button>
                                    </Stack>
                                </div>
                            </Form>
                        );
                    }}
                </Formik>
            </>
        );
    }
}

const defaultData = {
    country: '',
    organization: {
        id: null,
        name: '',
        source: '',
        country: null,
        city: '',
    },
    department: {
        id: null,
        name: '',
        source: '',
    },
    position: '',
};

const defaultValidation = {
    name: null,
    country: null,
    city: null,
    department: null,
    additionalInformation: null,
};

const AffiliationSchema = Yup.object().shape({
    affiliations: Yup.array()
        .of(
            Yup.object().shape({
                country: Yup.string().required('Please select a country'),
                organization: Yup.object().shape({
                    source: Yup.string().required('Please select an organization'),
                    name: Yup.string().when('source', {
                        is: 'manual',
                        then: Yup.string().required('Please enter a name'),
                    }),
                    city: Yup.string().when('source', {
                        is: 'manual',
                        then: Yup.string().required('Please enter a city'),
                    }),
                    id: Yup.string()
                        .nullable()
                        .when('source', {
                            is: !'manual',
                            then: Yup.string().required('Please select an organization'),
                        }),
                }),
                department: Yup.object().shape({
                    source: Yup.string().required('Please select a department'),
                    name: Yup.string().when('source', {
                        is: 'manual',
                        then: Yup.string().required('Please enter a name'),
                    }),
                    id: Yup.string()
                        .nullable()
                        .when('source', {
                            is: !'manual',
                            then: Yup.string().required('Please select a department'),
                        }),
                }),
                position: Yup.string().required('Please enter your position'),
            }),
        )
        .required('Please add an affiliation') // these constraints are shown if and only if inner constraints are satisfied
        .min(1, 'Please add an affiliation'),
});

export default withNotifications(AffiliationsForm);