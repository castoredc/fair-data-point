import React, {useEffect, useState} from "react";
import {Heading, LoadingOverlay} from "@castoredc/matter";
import DocumentTitle from "components/DocumentTitle";
import { toast } from "react-toastify";
import ToastContent from "components/ToastContent";
import PageBody from "components/Layout/Dashboard/PageBody";
import { apiClient } from "src/js/network";
import { EDCServersGrid } from "./EDCServersGrid";
import {ServerType} from "types/ServerType";

const EDCServers = ({history, location, match, user}) => {
    const title = "EDC Servers overview"

    return (
        <PageBody>
            <DocumentTitle title="EDC Servers" />

            <Heading type="Section">{title}</Heading>
            <EDCServersGrid />
        </PageBody>
    );
}

export {EDCServers};
