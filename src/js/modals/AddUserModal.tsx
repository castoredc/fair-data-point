import React, { Component } from 'react';
import { Button, Modal } from '@castoredc/matter';
import * as Yup from 'yup';
import { Field, Form, Formik } from 'formik';
import { FormikHelpers } from 'formik/dist/types';
import { mergeData } from '../util';
import FormItem from 'components/Form/FormItem';
import Input from 'components/Input/Formik/Input';
import Choice from 'components/Input/Formik/Choice';
import { ChoiceOptionProps } from '@castoredc/matter/lib/types/src/ChoiceOption/ChoiceOption';
import { PermissionType } from 'types/PermissionType';

type AddUserModalProps = {
    data: PermissionType | null;
    open: boolean;
    onClose: () => void;
    handleSubmit: (values: any, formikHelpers: FormikHelpers<any>) => void;
    permissions: ChoiceOptionProps[];
};

type AddUserModalState = {
    initialValues: any;
};

export default class AddUserModal extends Component<AddUserModalProps, AddUserModalState> {
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
            <Modal open={open} title={title} accessibleName={title} onClose={onClose}>
                <Formik initialValues={{
                    ...initialValues,
                    ...(permissions.length === 1 && {
                        type: permissions[0].value
                    }),
                }} validationSchema={edit ? UpdateUserSchema : NewUserSchema} onSubmit={handleSubmit}>
                    {({ values, errors, touched, handleChange, handleBlur, handleSubmit, isSubmitting, setValues }) => {
                        return (
                            <Form>
                                {!edit && (
                                    <FormItem label="Email address">
                                        <Field component={Input} name="email" readOnly={edit} autofocus />
                                    </FormItem>
                                )}

                                <FormItem label="Permissions">
                                    <Field component={Choice} collapse name="type" options={permissions} />
                                </FormItem>

                                <Button buttonType="primary" type="submit" disabled={isSubmitting}>
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
