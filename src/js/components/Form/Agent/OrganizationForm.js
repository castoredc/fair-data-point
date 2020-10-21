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

export default class OrganizationForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            axiosCancel: null,
            isLoading: false,
            defaultOptions: []
        };
    }

    componentDidMount() {
        const {data} = this.props;

        if(data.id !== null) {
            this.loadOrganizations(data.id, (data) => {
                this.setState({
                    defaultOptions: data
                })
            });
        }
    }

    loadOrganizations = (inputValue, callback) => {
        const {data} = this.props;
        const {axiosCancel} = this.state;

        if (data.country === null) {
            return null;
        }

        if (axiosCancel !== null) {
            axiosCancel.cancel();
        }

        const CancelToken = axios.CancelToken;
        const source = CancelToken.source();

        this.setState({
            axiosCancel: source,
        });

        axios.get('/api/agent/organization', {
            cancelToken: source.token,
            params: {
                country: data.country,
                search: inputValue,
            },
        }).then((response) => {
            callback(response.data);
        })
            .catch((error) => {
                if (!axios.isCancel(error)) {
                    if (error.response && typeof error.response.data.error !== "undefined") {
                        toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                    } else {
                        toast.error(<ToastContent type="error" message="An error occurred"/>);
                    }
                }
                callback(null);
            });
    };

    handleOrganizationChange = (event) => {
        const {handleChange, handleDataChange} = this.props;

        handleChange({target: {name: 'source', value: event.source}}, () => {
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
        const {countries, data, validation, handleChange} = this.props;
        const {defaultOptions} = this.state;

        const required = "This field is required";
        const invalid = "This value is invalid";

        const showForm = data.source === 'manual';

        return (
            <div className="Organization">
                <Stack>
                    <FormItem label="Country">
                        <Dropdown
                            validators={['required']}
                            errorMessages={[required]}
                            options={countries}
                            name="country"
                            onChange={(e) => {
                                handleChange({target: {name: 'country', value: e.value}})
                            }}
                            value={countries.filter(({value}) => value === data.country)}
                            menuPosition="fixed"
                        />
                    </FormItem>
                </Stack>
                <div className={classNames(data.country === null && 'WaitingOnInput')}>
                    <Stack>
                        {!showForm && <FormItem label="Organization / Institution">
                            <Dropdown
                                validators={['required']}
                                errorMessages={[required]}
                                async
                                name="id"
                                onChange={this.handleOrganizationChange}
                                loadOptions={this.loadOrganizations}
                                value={data.id}
                                menuPosition="fixed"
                                isDisabled={data.country === null}
                                defaultOptions={defaultOptions}
                            />

                            <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual}
                                    disabled={data.country === null}>
                                I cannot find my organization
                            </Button>
                        </FormItem>}

                        {showForm && <>
                            <FormItem label="Organization / Institution Name">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="name"
                                    onChange={handleChange}
                                    value={data.name}
                                    autoFocus
                                />

                                <Button buttonType="contentOnly" className="CannotFind" onClick={this.toggleManual}
                                        disabled={data.country === null}>
                                    Search for an organization
                                </Button>
                            </FormItem>
                            <FormItem label="City">
                                <Input
                                    validators={['required']}
                                    errorMessages={[required]}
                                    name="city"
                                    onChange={handleChange}
                                    value={data.city}
                                />
                            </FormItem>
                        </>}
                    </Stack>
                </div>
            </div>
        );
    }
}