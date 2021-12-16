import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import {RouteComponentProps} from "react-router-dom";
import {Heading, LoadingOverlay} from "@castoredc/matter";
import DocumentTitle from "components/DocumentTitle";
import FAIRDataPointMetadataForm from "components/Form/Metadata/FAIRDataPointMetadataForm";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";

interface FAIRDataPointProps extends RouteComponentProps<any> {
}

interface FAIRDataPointState {
    fdp: any,
    isLoading: boolean,
}

export default class FAIRDataPoint extends Component<FAIRDataPointProps, FAIRDataPointState> {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            fdp:          null,
        };
    }

    componentDidMount() {
        this.getFairDataPoint();
    }

    getFairDataPoint = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/fdp')
            .then((response) => {
                this.setState({
                    fdp:          response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred while loading the FAIR Data Point information"/>);
                }
            });
    };

    render() {
        const {fdp, isLoading} = this.state;

        const title = fdp ? (fdp.hasMetadata ? localizedText(fdp.metadata.title, 'en') : 'FAIR Data Point') : 'FAIR Data Point';

        return <div>
            <DocumentTitle title="FAIR Data Point" />

            {isLoading && <LoadingOverlay accessibleLabel="Loading FAIR Data Point information"/>}

            <Heading type="Section">{title}</Heading>

            {fdp && <FAIRDataPointMetadataForm fdp={fdp} onSave={this.getFairDataPoint} />}
        </div>;
    }
}