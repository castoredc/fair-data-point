import React, { Component } from 'react';

import './MetadataItem.scss';
import { toast } from 'react-toastify';
import {ToastMessage} from '@castoredc/matter';
import { apiClient } from 'src/js/network';

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

        apiClient
            .get('/api/license/' + slug)
            .then(response => {
                this.setState({
                    license: response.data,
                    isLoading: false,
                });
            })
            .catch(() => {
                this.setState({
                    isLoading: false,
                });

                toast.error(<ToastMessage type="error" title="Could not load license information." />);
            });
    };

    render() {
        const { license, isLoading } = this.state;

        if (isLoading || !license) {
            return <div className="License">&nbsp;</div>;
        }

        return <div className="License">{license.label}</div>;
    }
}
