import React from 'react';
import { classNames, localizedText } from '../../../util';
import ListItem from '../../../components/ListItem';
import Header from '../../../components/Layout/Header';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from 'utils/BreadcrumbUtils';
import { isGranted } from 'utils/PermissionHelper';
import LockIcon from '@mui/icons-material/Lock';
import useGetDistribution from '../../../hooks/useGetDistribution';
import { AuthorizedRouteComponentProps } from 'components/Route';
import MetadataSideBar from 'components/MetadataSideBar';
import MetadataDescription from 'components/MetadataSideBar/MetadataDescription';
import { AlertTitle } from '@mui/material';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import Grid from '@mui/material/Grid';

interface DistributionProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Distribution: React.FC<DistributionProps> = ({ user, embedded, location, match }) => {
    const {
        isLoading: isLoadingDistribution,
        distribution,
    } = useGetDistribution(match.params.dataset, match.params.distribution);

    const isLoading = isLoadingDistribution;
    const breadcrumbs = getBreadCrumbs(location, { distribution });
    const title = distribution ? localizedText(distribution.metadata.title, 'en') : null;

    return (
        <Layout className="Distribution" embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {distribution && (
                    <>
                        <Grid container spacing={2}>
                            <Grid size={8}>
                                <MetadataDescription metadata={distribution.metadata} />
                            </Grid>
                            <Grid size={4}>
                                <MetadataSideBar metadata={distribution.metadata} title={title} />
                            </Grid>
                        </Grid>

                        <div className="Separator"></div>

                        <div
                            className={classNames('DistributionAccess', !isGranted('access_data', distribution.permissions) && 'Restricted')}
                        >
                            {!user && !isGranted('access_data', distribution.permissions) && (
                                <div className="Overlay">
                                    <Alert
                                        icon={<LockIcon />}
                                        action={
                                            <Button
                                                color="inherit"
                                                variant="contained"
                                                onClick={() =>
                                                    (window.location.href =
                                                        '/connect/castor/' +
                                                        (distribution && distribution.study !== null ? distribution.study.sourceServer : '') +
                                                        '?target_path=' +
                                                        distribution.relativeUrl)}
                                            >
                                                Log in with Castor
                                            </Button>
                                        }
                                    >
                                        <AlertTitle>The access to the data in this distribution is
                                            restricted</AlertTitle>
                                        description="In order to access the data, please log in with your
                                        account.
                                    </Alert>
                                </div>
                            )}

                            {user && !isGranted('access_data', distribution.permissions) && (
                                <div className="Overlay">
                                    <Alert
                                        severity="error"
                                        icon={<LockIcon />}
                                    >
                                        <AlertTitle>You do not have access to the data inside this
                                            distribution</AlertTitle>
                                        The access to the data is restricted and your account has not been granted
                                        access.
                                    </Alert>
                                </div>
                            )}

                            {isGranted('access_data', distribution.permissions) && (
                                <div className="DistributionAccessButtons">
                                    {'cached' in distribution && distribution.cached && (
                                        <ListItem
                                            link={distribution.relativeUrl + '/query'}
                                            title="Query the data"
                                            description="Use SPARQL queries to extract specific information from this distribution."
                                            icon={isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock'}
                                            smallIcon={true}
                                            newWindow
                                        />
                                    )}

                                    {'accessUrl' in distribution && distribution.accessUrl && (
                                        <ListItem
                                            link={distribution.accessUrl}
                                            title="Access the data"
                                            description="Get access to the distribution."
                                            icon={isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock'}
                                            smallIcon={true}
                                            newWindow
                                        />
                                    )}

                                    {'downloadUrl' in distribution && distribution.downloadUrl && (
                                        <ListItem
                                            link={distribution.downloadUrl}
                                            title="Download the data"
                                            description="Get a downloadable file for this distribution."
                                            icon={isGranted('access_data', distribution.permissions) ? 'unlocked' : 'lock'}
                                            smallIcon={true}
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
};

export default Distribution;
