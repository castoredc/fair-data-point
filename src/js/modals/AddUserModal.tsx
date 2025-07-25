import React, { Component } from 'react';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import { FormikHelpers } from 'formik/dist/types';
import { mergeData } from '../util';
import FormItem from 'components/Form/FormItem';
import Input from 'components/Input/Formik/Input';
import Choice from 'components/Input/Formik/Choice';
import { PermissionType } from 'types/PermissionType';
import Button from '@mui/material/Button';
import Modal from 'components/Modal';
import Alert from '@mui/material/Alert';
import { Option } from 'components/RadioGroup';

type AddUserModalProps = {
    data: PermissionType | null;
    open: boolean;
    onClose: () => void;
    handleSubmit: (values: any, formikHelpers: FormikHelpers<any>) => void;
    permissions: Option[];
};

type AddUserModalState = {
    initialValues: any;
};

class AddUserModal extends Component<AddUserModalProps, AddUserModalState> {
    constructor(props) {
        super(props);

        this.state = {
            initialValues: defaultData,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { data, open } = this.props;

        if (open !== prevProps.open) {
            this.setState({
                initialValues: data ? mergeData(defaultData, data) : defaultData,
            });
        }
    }

    render() {
        const { open, data, onClose, handleSubmit, permissions } = this.props;
        const { initialValues } = this.state;

        const edit = !!data;
        const title = edit ? `Edit permissions for ${data.user.name}` : 'Add user';

        return (
            <Modal open={open} title={title} onClose={onClose}>
                <Formik
                    initialValues={{
                        ...initialValues,
                        ...(permissions.length === 1 && {
                            type: permissions[0].value,
                        }),
                    }}
                    validationSchema={edit ? UpdateUserSchema : NewUserSchema}
                    onSubmit={handleSubmit}
                >
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues }) => {
                        return (
                            <Form>
                                {!edit && <>
                                    <Alert severity="info">
                                        Please note that users need to log in to the FAIR Data Point first, before they
                                        can be invited.
                                    </Alert>
                                    <FormItem label="Email address">
                                        <Field component={Input} name="email" readOnly={edit} autofocus />
                                    </FormItem>
                                </>
                                }

                                <FormItem label="Permissions">
                                    <Field component={Choice} collapse name="type" options={permissions} />
                                </FormItem>

                                <Button type="submit" disabled={isSubmitting} variant="contained">
                                    {edit ? `Edit permissions` : 'Add user'}
                                </Button>
                            </Form>
                        );
                    }}
                </Formik>
            </Modal>
        );
    }
}

const NewUserSchema = Yup.object().shape({
    email: Yup.string().email('Please enter a valid email address').required('Please enter an email address'),
    type: Yup.string().required('Please select a permission type'),
});

const UpdateUserSchema = Yup.object().shape({
    type: Yup.string().required('Please select a permission type'),
});

const defaultData = {
    email: '',
    type: '',
};

export default AddUserModal;