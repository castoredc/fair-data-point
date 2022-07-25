import React, { Component } from "react";
import { Heading, LoadingOverlay } from "@castoredc/matter";
import DocumentTitle from "components/DocumentTitle";
import { toast } from "react-toastify";
import ToastContent from "components/ToastContent";
import { AuthorizedRouteComponentProps } from "components/Route";
import PageBody from "components/Layout/Dashboard/PageBody";
import { apiClient } from "src/js/network";

interface EDCServersProps extends AuthorizedRouteComponentProps {}

interface EDCServersState {
    edcServers: any;
    isLoading: boolean;
}

export default class EDCServers extends Component<
    EDCServersProps,
    EDCServersState
    > {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            edcServers: null,
        };
    }

    componentDidMount() {
        this.getEDCServers();
    }

    getEDCServers = () => {
        this.setState({
            isLoading: true,
        });

        // @TODO Create backend endpoint and load the data here.
        // apiClient
        //     .get("/api/edc-servers")
        //     .then((response) => {
        //         this.setState({
        //             edcServers: response.data,
        //             isLoading: false,
        //         });
        //     })
        //     .catch((error) => {
        //         this.setState({
        //             isLoading: false,
        //         });
        //
        //         if (
        //             error.response &&
        //             typeof error.response.data.error !== "undefined"
        //         ) {
        //             toast.error(
        //                 <ToastContent type="error" message={error.response.data.error} />
        //             );
        //         } else {
        //             toast.error(
        //                 <ToastContent
        //                     type="error"
        //                     message="An error occurred while loading the EDC Servers information"
        //                 />
        //             );
        //         }
        //     });

        this.setState({
            isLoading: false,
        });

    };

    render() {
        const { /*edcServers, */isLoading } = this.state;

        const title = "EDC Servers overview"

        return (
            <PageBody>
                <DocumentTitle title="EDC Servers" />

                {isLoading && (
                    <LoadingOverlay accessibleLabel="Loading EDC servers information" />
                )}

                <Heading type="Section">{title}</Heading>

                {/*{fdp && (*/}
                {/*    <FAIRDataPointMetadataForm fdp={fdp} onSave={this.getFairDataPoint} />*/}
                {/*)}*/}
            </PageBody>
        );
    }
}
