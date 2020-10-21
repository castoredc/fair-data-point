import React, {Component} from "react";
import './Affiliations.scss';
import Emoji from "../../../components/Emoji";
import {Button, CastorLogo, Stack} from "@castoredc/matter";
import FormItem from "../../../components/Form/FormItem";
import Input from "../../../components/Input";
import {ValidatorForm} from "react-form-validator-core";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import queryString from "query-string";

export default class Affiliations extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data:      this.parseUserDetails(props.user),
            isLoading: false,
            isSaved:   false,
        };
    }

    parseUserDetails = (user) => {
        let details = {
            firstName:  '',
            middleName: '',
            lastName:   '',
            email:      '',
        }

        if(typeof user.suggestions !== "undefined")
        {
            details.firstName = user.suggestions.firstName;
            details.lastName = user.suggestions.lastName;
        }

        if(user.details !== null)
        {
            details.firstName = user.details.firstName;
            details.middleName = user.details.middleName;
            details.lastName = user.details.lastName;
            details.email = user.details.email;
        }

        return details;
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
            // window.location.href = (typeof params.origin !== 'undefined') ? params.origin : '/';
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
                    <Emoji symbol="🏥"/>&nbsp;
                    Where do you work, {data.firstName}?
                </h1>
                <div className="Description">
                    Please add your affiliation(s) below.
                </div>
            </header>


            xxx
        </>;
    }
}
