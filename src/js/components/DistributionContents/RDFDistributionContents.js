import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";

export default class RDFDistributionContents extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingNodes:     true,
            hasLoadedNodes:     false,
            nodes:              null,
        };
    }

    componentDidMount() {
        this.getValueNodes();
    }

    getValueNodes = () => {
        const { model } = this.props;
        this.setState({
            isLoadingNodes: true,
        });

        axios.get('/api/model/' + model.id + '/node/value')
            .then((response) => {
                this.setState({
                    nodes:          response.data.elements,
                    isLoadingNodes: false,
                    hasLoadedNodes: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingNodes: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the nodes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {

    }
}