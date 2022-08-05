import React, {useEffect, useState} from "react";
import {Heading, LoadingOverlay} from "@castoredc/matter";
import DocumentTitle from "components/DocumentTitle";
import { toast } from "react-toastify";
import ToastContent from "components/ToastContent";
import PageBody from "components/Layout/Dashboard/PageBody";
import { apiClient } from "src/js/network";
import EDCServersGrid from "./EDCServersGrid";
import {ServerType} from "types/ServerType";

const EDCServers = ({history, location, match, user}) => {
    const [isLoading, setIsLoading] = useState(false)
    const [edcServers, setEdcServers] = useState<ServerType[]>([]);

    const getEDCServers = () => {
        setIsLoading(true);

        apiClient
            .get("/api/castor/servers")
            .then((response) => {
                setIsLoading(false);
                setEdcServers(response.data);
            })
            .catch((error) => {
                setIsLoading(false);
                if (
                    error.response &&
                    typeof error.response.data.error !== "undefined"
                ) {
                    toast.error(
                        <ToastContent type="error" message={error.response.data.error}/>
                    );
                } else {
                    toast.error(
                        <ToastContent
                            type="error"
                            message="An error occurred while loading the EDC Servers information"
                        />
                    );
                }
            });
    };
    useEffect( () => {
        getEDCServers();
    }, []);
    const title = "EDC Servers overview"

    return (
        <PageBody>
            <DocumentTitle title="EDC Servers" />

            {isLoading && (
                <LoadingOverlay accessibleLabel="Loading EDC servers information" />
            )}

            <Heading type="Section">{title}</Heading>

            {edcServers.length && (
                <EDCServersGrid edcServers={edcServers} />
            )}
        </PageBody>
    );
}

export {EDCServers};
