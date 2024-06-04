import React, { useState } from 'react';
import './MetadataSideBar.scss';
import MetadataItem from '../MetadataItem';
import Publishers from '../MetadataItem/Publishers';
import Language from '../MetadataItem/Language';
import License from '../MetadataItem/License';
import moment from 'moment';
import { Button, Modal, Tooltip } from '@castoredc/matter';
import { MethodType, StudyType } from '../MetadataItem/EnumMappings';
import Organizations from '../MetadataItem/Organizations';

const MetadataMapping = {
    fdp: ['publishers', 'version', 'language', 'license', 'issued', 'modified'],
    catalog: ['publishers', 'version', 'language', 'license', 'issued', 'modified'],
    dataset: ['publishers', 'version', 'language', 'license', 'issued', 'modified'],
    distribution: ['publishers', 'version', 'language', 'license', 'issued', 'modified'],
    study: ['logo', 'studyType', 'methodType', 'estimatedEnrollment', 'organizations', 'version', 'issued', 'modified'],
};

const LegacyMetadataSideBar = ({ name, type, metadata }) => {
    const [isOpen, setOpen] = useState(false);

    const shouldDisplay = item => {
        return metadata && MetadataMapping[type].includes(item) && metadata[item];
    };

    return (
        <div className="MetadataSideBar">
            {shouldDisplay('logo') && (
                <div className="InformationLogo">
                    <img src={metadata.logo} alt={'Logo'} />
                </div>
            )}

            {shouldDisplay('studyType') && <MetadataItem label="Type" value={StudyType[metadata.studyType]} />}
            {shouldDisplay('methodType') && <MetadataItem label="Method" value={MethodType[metadata.methodType]} />}
            {shouldDisplay('estimatedEnrollment') && <MetadataItem label="Estimated Enrollment" value={metadata.estimatedEnrollment} />}
            {shouldDisplay('organizations') && metadata.organizations.length > 0 && <Organizations organizations={metadata.organizations} />}

            {shouldDisplay('publishers') && (
                <MetadataItem label="Publishers">
                    <Publishers publishers={metadata.publishers} />
                </MetadataItem>
            )}

            <Button icon="openNewWindow" buttonType="bare" onClick={() => setOpen(true)}>
                View metadata
            </Button>

            <Modal accessibleName="Test" open={isOpen} title={`Metadata for ${name}`} onClose={() => setOpen(false)} className="MetadataModal">
                {shouldDisplay('studyType') && <MetadataItem label="Type" value={StudyType[metadata.studyType]} table />}
                {shouldDisplay('methodType') && <MetadataItem label="Method" value={MethodType[metadata.methodType]} table />}
                {shouldDisplay('estimatedEnrollment') && <MetadataItem label="Estimated Enrollment" value={metadata.estimatedEnrollment} table />}
                {shouldDisplay('organizations') && metadata.organizations.length > 0 && (
                    <Organizations organizations={metadata.organizations} table />
                )}

                {shouldDisplay('publishers') && (
                    <MetadataItem label="Publishers" table>
                        <Publishers publishers={metadata.publishers} />
                    </MetadataItem>
                )}
                {shouldDisplay('license') && (
                    <MetadataItem label="License" table>
                        <License slug={metadata.license} />
                    </MetadataItem>
                )}

                {shouldDisplay('language') && (
                    <MetadataItem label="Language" table>
                        <Language code={metadata.language} />
                    </MetadataItem>
                )}

                {shouldDisplay('version') && (
                    <>
                        <hr />
                        <MetadataItem label="Metadata version" value={metadata.version.metadata} table />
                    </>
                )}

                {shouldDisplay('issued') && (
                    <MetadataItem label="Issued" table>
                        <Tooltip content={moment(metadata.issued).format('DD-MM-YYYY HH:mm:ss')}>
                            {moment(metadata.issued).format('DD-MM-YYYY')}
                        </Tooltip>
                    </MetadataItem>
                )}
                {shouldDisplay('modified') && (
                    <MetadataItem label="Modified" table>
                        <Tooltip content={moment(metadata.modified).format('DD-MM-YYYY HH:mm:ss')}>
                            {moment(metadata.modified).format('DD-MM-YYYY')}
                        </Tooltip>
                    </MetadataItem>
                )}
            </Modal>
        </div>
    );
};

export default LegacyMetadataSideBar;
