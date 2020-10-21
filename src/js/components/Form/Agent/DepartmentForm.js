import React, {Component} from 'react';

import '../Form.scss'
import FormItem from "../FormItem";
import Dropdown from "../../Input/Dropdown";
import {Button, Stack} from "@castoredc/matter";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../ToastContent";
import Input from "../../Input";
import {classNames} from "../../../util";

export default class DepartmentForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoading: false,
            options: []
        };
    }

    componentDidUpdate(prevProps) {
        const {organization} = this.props;

        if (organization.id !== prevProps.organization.id) {
            this.getDepartments();
        }
    }

    componentDidMount() {
        this.getDepartments();
    }

    getDepartments = () => {
        const {organization, handleChange} = this.props;

        if (organization.id !== null) {
            this.setState({
                isLoading: true,
            });

            axios.get('/api/agent/organization/' + organization.id + '/department')
                .then((response) => {
                    this.setState({
                        options: response.data.map((department) => {
                            return {value: department.id, label: department.name, data: department};
                        }),
                        isLoading: false,
                    });
                })
                .catch((error) => {
                    this.setState({
                        isLoading: false
                    });

                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>);
                    }
                });
        }
        else {
            this.setState({
                options: [],
            });
        }
    };

    handleDepartmentChange = (event) => {
        const {handleChange, handleDataChange} = this.props;

        handleChange({target: {name: 'source', value: 'database'}}, () => {
            handleDataChange(event.data);
        });
    }

    toggleManual = () => {
        const {handleChange, data} = this.props;

        const source = (data.source === 'manual') ? null : 'manual';

        handleChange({target: {name: 'source', value: source}}, () => {
            if (source === 'manual') {
                handleChange({target: {name: 'id', value: null}});
            }
        });
    }

    render() {
        const {options} = this.state;
        const {data, validation, handleChange, organization} = this.props;

        const required = "This field is required";

        const showForm = data.source === 'manual';

        return (
            <div className={classNames('Department', organization.source === null && 'WaitingOnInput')}>
                <Stack>
                {!showForm && <FormItem label="Department">
                    <Dropdown
                        validators={['required']}
                        errorMessages={[required]}
                        name="id"
                        onChange={this.handleDepartmentChange}
                        value={data.id}
                        menuPosition="fixed"
                        options={options}
                        isDisabled={organization.source === null}
                    />

                    <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual}
                            disabled={organization.source === null}>
                        I cannot find my department
                    </Button>
                </FormItem>}

                {showForm && <>
                    <FormItem label="Department Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="name"
                            onChange={handleChange}
                            value={data.name}
                        />

                        {organization.id !== null && <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual}
                                disabled={data.country === null}>
                            Search for an department
                        </Button>}
                    </FormItem>
                </>}
                </Stack>
            </div>
        );
    }
}