import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import FormItem from "../../components/Form/FormItem";
import {Button, Stack} from "@castoredc/matter";
import Modal from "../Modal";
import RadioGroup from "../../components/Input/RadioGroup";
import PersonForm from "../../components/Form/Agent/PersonForm";
import OrganizationForm from "../../components/Form/Agent/OrganizationForm";
import ConfirmModal from "../ConfirmModal";
import DepartmentForm from "../../components/Form/Agent/DepartmentForm";

export default class PublisherModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            type: props.type ? props.type : 'person',
            data: props.data ? props.data : defaultData,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {show, data, type} = this.props;

        if (show !== prevProps.show || data !== prevProps.data) {
            this.setState({
                type: type ? type : 'person',
                data: data ? data : defaultData,
            })
        }
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isUrl', (value) => {
            var pattern = new RegExp('^((ft|htt)ps?:\\/\\/)?' + // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name and extension
                '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
                '(\\:\\d+)?' + // port
                '(\\/[-a-z\\d%@_.~+&:]*)*' + // path
                '(\\?[;&a-z\\d%@_.,~+&:=-]*)?' + // query string
                '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
            return pattern.test(value);
        });
    }

    handleTypeChange = (event) => {
        this.setState({
            type: event.target.value,
            data: defaultData
        });
    };

    handleChange = (group, event, callback = () => {
    }) => {
        const {data} = this.state;

        const newData = {
            ...data,
            [group]: {
                ...data[group],
                [event.target.name]: event.target.value,
            }
        };

        this.setState({
            data: newData
        }, callback);
    };

    handleDataChange = (group, newData) => {
        const {data} = this.state;

        this.setState({
            data: {
                ...data,
                [group]: {
                    ...data[group],
                    ...newData
                }
            }
        });
    };

    handleSubmit = () => {
        const {save, edit = false} = this.props;
        const {type, data} = this.state;

        const newData = type === 'person' ? {
            person: data.person
        } : {
            organization: data.organization,
            department: data.department
        };

        if (this.form.isFormValid() && !edit) {
            save({
                type: type,
                ...newData
            });
        }
    };

    render() {
        const {show, handleClose, countries, deletePublisher, edit = false} = this.props;
        const {type, data, isLoading} = this.state;

        return <Modal
            show={show}
            handleClose={handleClose}
            title={edit ? `Edit publisher (${type})` : 'Add publisher'}
            closeButton
            footer={(
                <Stack>
                    {(!edit || (edit && type === 'organization')) &&
                    <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                        {edit ? 'Edit publisher' : 'Add publisher'}
                    </Button>}
                    {edit && <ConfirmModal
                        title="Delete publisher"
                        action="Delete publisher"
                        variant="danger"
                        onConfirm={deletePublisher}
                        includeButton={true}
                    >
                        Are you sure you want to delete this publisher?
                    </ConfirmModal>}
                </Stack>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                {!edit && <FormItem label="Type">
                    <RadioGroup
                        options={[
                            {
                                label: 'Person',
                                value: 'person',
                            },
                            {
                                label: 'Organization',
                                value: 'organization',
                            },
                        ]}
                        onChange={this.handleTypeChange}
                        value={type}
                        name="type"
                        variant="horizontal"
                    />
                </FormItem>}

                {type === 'person' && <PersonForm
                    data={data.person}
                    edit={edit}
                    handleChange={(event, callback) => this.handleChange('person', event, callback)}
                    handleDataChange={(newData) => this.handleDataChange('person', newData)}
                />}

                {type === 'organization' && <>
                    <OrganizationForm
                        data={data.organization}
                        countries={countries}
                        handleChange={(event, callback) => this.handleChange('organization', event, callback)}
                        handleDataChange={(newData) => this.handleDataChange('organization', newData)}
                    />

                    <DepartmentForm
                        data={data.department}
                        organization={data.organization}
                        handleChange={(event, callback) => this.handleChange('department', event, callback)}
                        handleDataChange={(newData) => this.handleDataChange('department', newData)}
                    />
                </>}
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    person: {
        id:         null,
        firstName:  '',
        middleName: '',
        lastName:   '',
        email:      '',
        orcid:      '',
    },
    organization: {
        id: null,
        name: '',
        source: null,
        country: null,
        city: '',
    },
    department: {
        id: null,
        name: '',
        source: null,
    },
};