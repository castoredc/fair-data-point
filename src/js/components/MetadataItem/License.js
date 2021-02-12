import React, {Component} from 'react'

import './MetadataItem.scss'
import {classNames} from "../../util";
import {Icon} from "@castoredc/matter";
import Department from "./Department";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";

export default class License extends Component {
    constructor(props) {
        super(props);

        this.state = {
            license: null,
            isLoading: true,
        };
    }

    componentDidMount() {
        this.getLicense();
    }

    getLicense = () => {
        const { slug } = this.props;

        axios.get('/api/license/' + slug)
            .then((response) => {
                this.setState({
                    license: response.data,
                    isLoading: false,
                });
            })
            .catch(() => {
                this.setState({
                    isLoading: false,
                });

                toast.error(<ToastContent type="error" message="Could not load license information."/>);
            });
    };

    render() {
        const { license, isLoading } = this.state;

        if(isLoading || ! license) {
            return <div className="License">&nbsp;</div>;
        }

        return <div className="License">
            {license.label}
        </div>;
    }
}