import React, {Component} from 'react'
import './FullScreenSteppedForm.scss'
import {localizedText} from "../../util";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import FullScreenSteppedForm from "./FullScreenSteppedForm";
import LoadingScreen from "../LoadingScreen";

export default class CatalogSteppedForm extends Component {
    constructor(props) {
        super(props);

        this.state = {
            catalog: null,
            isLoading: true
        };
    }

    getCatalog = () => {
        axios.get('/api/catalog/' + this.props.catalog)
            .then((response) => {
                this.setState({
                    catalog:   response.data,
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });
                toast.error(<ToastContent type="error" message="An error occurred"/>);
            });
    };

    componentDidMount() {
        this.getCatalog();
    }

    render() {
        const {smallHeading, heading, description, currentStep, children} = this.props;

        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FullScreenSteppedForm
            brandText={localizedText(this.state.catalog.title, 'en')}
            smallHeading={smallHeading}
            heading={heading}
            description={description}
            numberOfSteps={4}
            currentStep={currentStep}
            >
            {children}
        </FullScreenSteppedForm>;

    }
}