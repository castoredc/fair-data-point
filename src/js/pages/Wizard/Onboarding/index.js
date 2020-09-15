import React, {Component} from "react";
import './Onboarding.scss';
import Emoji from "../../../components/Emoji";
import {Button, CastorLogo, Stack} from "@castoredc/matter";
import FormItem from "../../../components/Form/FormItem";
import Input from "../../../components/Input";
import {ValidatorForm} from "react-form-validator-core";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import queryString from "query-string";

export default class Onboarding extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data:      {
                firstName:  props.user.details.firstName ?? '',
                middleName: props.user.details.middleName ?? '',
                lastName:   props.user.details.lastName ?? '',
                email:      props.user.details.email ?? '',
            },
            isLoading: false,
            isSaved:   false,
        };
    }

    handleChange = (event) => {
        const {data} = this.state;

        const newState = {
            data: {
                ...data,
                [event.target.name]: event.target.value,
            },
        };

        this.setState(newState);
    };

    handleSubmit = (event) => {
        const {data} = this.state;

        event.preventDefault();

        if (this.form.isFormValid()) {
            this.setState({
                isLoading: true,
            });

            axios.post('/api/user', data)
                .then((response) => {
                    this.setState({
                        isSaved:   true,
                        isLoading: false,
                    });
                })
                .catch((error) => {
                    if (error.response && error.response.status === 400) {
                        this.setState({
                            validation: error.response.data.fields,
                        });
                    } else {
                        toast.error(<ToastContent type="error"
                                                  message="An error occurred while updating your details"/>, {
                            position: "top-center",
                        });
                    }
                    this.setState({
                        isLoading: false,
                    });
                });
        }

        return false;
    };

    render() {
        const {user, location} = this.props;
        const {data, isLoading, isSaved} = this.state;

        const required = "This field is required";
        const invalid = "This value is invalid";

        if (isSaved) {
            const params = queryString.parse(location.search);

            window.location.href = (typeof params.origin !== 'undefined') ? params.origin : '/';
        }

        return <>

            <div className="WizardBrand">
                <div className="WizardBrandLogo">
                    <CastorLogo className="Logo"/>
                </div>
                <div className="WizardBrandText">
                    FAIR Data Point
                </div>
            </div>

            <header>
                <h1>
                    <Emoji symbol="👋"/>&nbsp;
                    Hi {user.details.firstName}!
                </h1>
                <div className="Description">
                    Before you continue, please check your details below.
                </div>
            </header>


            <ValidatorForm
                className="FullHeightForm"
                ref={node => (this.form = node)}
                onSubmit={this.handleSubmit}
                method="post"
            >
                {user.details.nameOrigin === 'orcid' && <>
                    <FormItem label="First Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="firstName"
                            onChange={this.handleChange}
                            value={data.firstName}
                        />
                    </FormItem>
                    <FormItem label="Middle Name">
                        <Input
                            name="middleName"
                            onChange={this.handleChange}
                            value={data.middleName}
                        />
                    </FormItem>
                    <FormItem label="Last Name">
                        <Input
                            validators={['required']}
                            errorMessages={[required]}
                            name="lastName"
                            onChange={this.handleChange}
                            value={data.lastName}
                        />
                    </FormItem>
                </>}

                <FormItem label="Email address">
                    <Input
                        validators={['required', 'isEmail']}
                        errorMessages={[required, invalid]}
                        name="email"
                        onChange={this.handleChange}
                        value={data.email}
                    />
                </FormItem>


                <div className="FormButtons">
                    <Stack distribution="trailing">
                        <Button type="submit" disabled={isLoading}>
                            Save details
                        </Button>
                    </Stack>
                </div>
            </ValidatorForm>
        </>;
    }
}
