import React, {Component} from 'react'

import './MetadataItem.scss'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";

export default class Language extends Component {
    constructor(props) {
        super(props);

        this.state = {
            language: null,
            isLoading: true,
        };
    }

    componentDidMount() {
        this.getLanguage();
    }

    getLanguage = () => {
        const { code } = this.props;

        axios.get('/api/language/' + code)
            .then((response) => {
                this.setState({
                    language: response.data,
                    isLoading: false,
                });
            })
            .catch(() => {
                this.setState({
                    isLoading: false,
                });

                toast.error(<ToastContent type="error" message="Could not load language information."/>);
            });
    };

    render() {
        const { language, isLoading } = this.state;

        if(isLoading || ! language) {
            return <div className="Language">&nbsp;</div>;
        }

        return <div className="Language">
            {language.label}
        </div>;
    }
}