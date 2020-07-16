import React, {Component} from 'react'
import {ValidatorForm} from "react-form-validator-core";
import Input from "../../components/Input";
import FormItem from "../../components/Form/FormItem";
import RadioGroup from "../../components/Input/RadioGroup";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {Button} from "@castoredc/matter";
import Dropdown from "../../components/Input/Dropdown";
import Modal from "../Modal";
import {DataType} from "../../components/MetadataItem/EnumMappings";

export default class AddNodeModal extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: defaultData,
            validation: {},
            showFieldSelector: false,
            isLoading: false
        };
    }

    componentDidMount() {
        ValidatorForm.addValidationRule('isUrl', (value) => {
            var pattern = new RegExp('^((ft|htt)ps?:\\/\\/)?'+ // protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name and extension
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
                '(\\:\\d+)?'+ // port
                '(\\/[-a-z\\d%@_.~+&:]*)*'+ // path
                '(\\?[;&a-z\\d%@_.,~+&:=-]*)?'+ // query string
                '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
            return pattern.test(value);
        });
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if (this.props.show !== prevProps.show) {
            this.setState({
                data: defaultData
            })
        }
    }

    handleChange = (event, callback = (() => {})) => {
        const { data } = this.state;
        this.setState({
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
            validation: {
                [event.target.name]: false,
            }
        }, callback);
    };

    handleDataTypeChange = (event) => {
        const { data } = this.state;

        this.setState({
            data: {
                ...data,
                dataType: event.value
            }
        });
    };

    handleSubmit = () => {
        const {type, modelId, versionId, onSaved} = this.props;
        const {data} = this.state;

        if (this.form.isFormValid()) {
            if (this.form.isFormValid()) {
                this.setState({isLoading: true});

                axios.post('/api/model/' + modelId + '/v/' + versionId + '/node/' + type + '/add', data)
                    .then((response) => {
                        this.setState({
                            isLoading: false,
                        });

                        onSaved();
                    })
                    .catch((error) => {
                        if (error.response && error.response.status === 400) {
                            this.setState({
                                validation: error.response.data.fields
                            });
                        } else {
                            toast.error(<ToastContent type="error" message="An error occurred"/>, {
                                position: "top-center"
                            });
                        }
                        this.setState({isLoading: false});
                    });
            }
        }
    };

    render() {
        const { type, show, handleClose } = this.props;
        const { data, validation, isLoading } = this.state;

        const required = "This field is required";
        const validUrl = "Please enter a valid URI";

        const showDataTypes = (type === 'literal' || (type === 'value' && data.value === 'plain'));

        return <Modal
            show={show}
            handleClose={handleClose}
            className="AddNodeModal"
            title={`Add ${type} node`}
            closeButton
            footer={(
                <Button type="submit" disabled={isLoading} onClick={() => this.form.submit()}>
                    Add node
                </Button>
            )}
        >
            <ValidatorForm
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                <FormItem label="Title">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="title"
                        onChange={this.handleChange}
                        value={data.title}
                        serverError={validation.title}
                    />
                </FormItem>
                <FormItem label="Description">
                    <Input
                        name="description"
                        onChange={this.handleChange}
                        value={data.description}
                        serverError={validation.description}
                        as="textarea" rows="3"
                    />
                </FormItem>
                {type === 'external' && <FormItem label="URI">
                    <Input
                        validators={['required', 'isUrl']}
                        errorMessages={[required, validUrl]}
                        name="value"
                        onChange={this.handleChange}
                        value={data.value}
                        serverError={validation.value}
                    />
                </FormItem>}
                {type === 'internal' && <FormItem label="Slug">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="value"
                        onChange={this.handleChange}
                        value={data.value}
                        serverError={validation.value}
                    />
                </FormItem>}
                {type === 'literal' && <FormItem label="Value">
                    <Input
                        validators={['required']}
                        errorMessages={[required]}
                        name="value"
                        onChange={this.handleChange}
                        value={data.value}
                        serverError={validation.value}
                    />
                </FormItem>}
                {type === 'value' && <>
                    <FormItem label="Value">
                        <RadioGroup
                            validators={['required']}
                            errorMessages={[required]}
                            options={[
                                { value: 'plain', label: 'Plain value' },
                                { value: 'annotated', label: 'Annotated value' },
                            ]}
                            onChange={this.handleChange}
                            value={data.value}
                            serverError={validation.value}
                            name="value"
                            variant="horizontal"
                        />
                    </FormItem>
                    <FormItem label="Repeated">
                        <RadioGroup
                            validators={['required']}
                            errorMessages={[required]}
                            options={[
                                {
                                    label: 'Yes',
                                    value: true
                                },
                                {
                                    label: 'No',
                                    value: false
                                }
                            ]}
                            onChange={this.handleChange}
                            value={data.repeated}
                            serverError={validation.repeated}
                            name="repeated"
                            variant="horizontal"
                        />
                    </FormItem>
                </>}
                {showDataTypes && <FormItem label="Data type">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        options={dataTypes}
                        onChange={this.handleDataTypeChange}
                        value={dataTypes.find((dataType) => {return data.dataType === dataType.value})}
                        serverError={validation.dataType}
                        name="dataType"
                        menuPosition="fixed"
                    />
                </FormItem>}
            </ValidatorForm>
        </Modal>
    }
}

const defaultData = {
    title: '',
    description: '',
    value: '',
    dataType: null,
    repeated: false
};

const dataTypes = [
    { value: 'float', label: DataType['float'] },
    { value: 'double', label: DataType['double'] },
    { value: 'decimal', label: DataType['decimal'] },
    { value: 'integer', label: DataType['integer'] },
    { value: 'dateTime', label: DataType['dateTime'] },
    { value: 'date', label: DataType['date'] },
    { value: 'time', label: DataType['time'] },
    { value: 'gDay', label: DataType['gDay'] },
    { value: 'gMonth', label: DataType['gMonth'] },
    { value: 'gYear', label: DataType['gYear'] },
    { value: 'gYearMonth', label: DataType['gYearMonth'] },
    { value: 'gMonthDay', label: DataType['gMonthDay'] },
    { value: 'string', label: DataType['string'] },
    { value: 'boolean', label: DataType['boolean'] },
];