import React from 'react';
import { classNames } from '../../../util';
import ListItem from '../../../components/ListItem';
import Header from '../../../components/Layout/Header';
import { Banner } from '@castoredc/matter';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import LegacyMetadataSideBar from '../../../components/MetadataSideBar/LegacyMetadataSideBar';
import './Distribution.scss';
import { isGranted } from 'utils/PermissionHelper';
import { LockIcon } from '@castoredc/matter-icons';
import useJsonLdRepresentation from '../../../hooks/useJsonLdRepresentation';
import { localizedText, titleAndDescriptionContext } from 'utils/jsonLdUtils';
import useGetDistribution from '../../../hooks/useGetDistribution';
import { AuthorizedRouteComponentProps } from 'components/Route';

interface DistributionProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Distribution: React.FC<DistributionProps> = ({ user, embedded, location, match }) => {
    const { isLoading: isLoadingDistribution, distribution } = useGetDistribution(match.params.dataset, match.params.distribution);
    const { data, isLoading: isLoadingJsonLd } = useJsonLdRepresentation(`${window.location.pathname}?format=jsonld`, titleAndDescriptionContext);

    const isLoading = isLoadingDistribution || isLoadingJsonLd;
    const breadcrumbs = getBreadCrumbs(location, { distribution });
    const restricted = distribution && (distribution.accessRights === 2 || distribution.accessRights === 3);
    const title = localizedText(data.title, 'en');

    return (
        <Layout className="Distribution" title={title} isLoading={isLoading} embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {distribution && (
                    <>
                        <div className="MainCol">
                            {data.description && (
                                <div className="InformationDescription">{localizedText(data.description, 'en', true)}</div>
                            )}
                        </div>

                        <div className="SideCol">
                            <LegacyMetadataSideBar type="distribution" metadata={distribution.metadata} name={title} />
                        </div>

                        <hr className="Separator" />

                        <div
                            className={classNames(
                                'MainCol DistributionAccess',
                                !isGranted('access_data', distribution.permissions) && 'Restricted'
                            )}
                        >
                            {!user && !isGranted('access_data', distribution.permissions) && (
                                <div className="Overlay">
                                    <Banner
                                        customIcon={<LockIcon />}
                                        title="The access to the data in this distribution is restricted"
                                        description="In order to access the data, please log in with your Castor CDMS account."
                                        actions={[
                                            {
                                                label: 'Log in with Castor',
                                                onClick: () =>
                                                    (window.location.href =
                                                        '/connect/castor/' +
                                                        distribution.study.sourceServer +
                                                        '?target_path=' +
                                                        distribution.relativeUrl),
                                            },
                                        ]}
                                    />
                                </div>
                            )}

                            {user && !isGranted('access_data', distribution.permissions) && (
                                <div className="Overlay">
                                    <Banner
                                        type="error"
                                        customIcon={<LockIcon />}
                                        title="You do not have access to the data inside this distribution"
                                        description="The access to the data is restricted and your account has not been granted access."
                                    />
                                </div>
                            )}

                            {isGranted('access_data', distribution.permissions) && (
                                <div className="DistributionAccessButtons">
                                    {distribution.cached && (
                                        <ListItem
                                            link={distribution.relativeUrl + '/query'}
                                            title="Query the data"
                                            description="Use SPARQL queries to extract specific information from this distribution."
                                            smallIcon={restricted && (isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock')}
                                            newWindow
                                        />
                                    )}

                                    {distribution.accessUrl && (
                                        <ListItem
                                            link={distribution.accessUrl}
                                            title="Access the data"
                                            description="Get access to the distribution."
                                            smallIcon={restricted && (isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock')}
                                            newWindow
                                        />
                                    )}

                                    {distribution.downloadUrl && (
                                        <ListItem
                                            link={distribution.downloadUrl}
                                            title="Download the data"
                                            description="Get a downloadable file for this distribution."
                                            smallIcon={restricted && (isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock')}
                                            newWindow
                                        />
                                    )}
                                </div>
                            )}
                        </div>
                    </>
                )}
            </MainBody>
        </Layout>
    );
}

export default Distribution;