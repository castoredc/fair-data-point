import React from 'react'
import './MetadataSideBar.scss';
import MetadataItem from "../MetadataItem";
import Publishers from "../MetadataItem/Publishers";
import Language from "../MetadataItem/Language";
import License from "../MetadataItem/License";
import MetadataItemGroup from "../MetadataItem/MetadataItemGroup";
import moment from "moment";
import {Tooltip} from "@castoredc/matter";

const MetadataMapping = {
    fdp: ["publishers", "version", "language", "license", "issued", "modified"],
    catalog: ["publishers", "version", "language", "license", "issued", "modified"],
    dataset: ["publishers", "version", "language", "license", "issued", "modified"],
    distribution: ["publishers", "version", "language", "license", "issued", "modified"],
}

const MetadataSideBar = ({type, metadata}) => {
    const shouldDisplay = (item) => {
        return (MetadataMapping[type].includes(item) && metadata[item]);
    }

    return <div className="MetadataSideBar">
        {shouldDisplay('publishers') && <MetadataItem label="Publishers">
            <Publishers publishers={metadata.publishers}/>
        </MetadataItem>}
        {shouldDisplay('language') && <MetadataItem label="Language">
            <Language code={metadata.language} />
        </MetadataItem>}
        {shouldDisplay('license') && <MetadataItem label="License">
            <License slug={metadata.license} />
        </MetadataItem>}

        {shouldDisplay('version') && <>
            <hr />
            <MetadataItem label="Metadata version" value={metadata.version.metadata}/>
        </>}

        {(shouldDisplay('issued') || shouldDisplay('modified')) && <MetadataItemGroup>
            {shouldDisplay('issued') && <MetadataItem label="Issued">
                <Tooltip content={moment(metadata.issued).format('DD-MM-YYYY HH:mm:ss')}>
                    {moment(metadata.issued).format('DD-MM-YYYY')}
                </Tooltip>
            </MetadataItem>}
            {shouldDisplay('modified') && <MetadataItem label="Modified">
                <Tooltip content={moment(metadata.modified).format('DD-MM-YYYY HH:mm:ss')}>
                    {moment(metadata.modified).format('DD-MM-YYYY')}
                </Tooltip>
            </MetadataItem>}
        </MetadataItemGroup>}
    </div>;
}

export default MetadataSideBar;