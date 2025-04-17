import React from 'react';
import { localizedText } from '../../../util';
import Layout from '../../../components/Layout';
import Header from '../../../components/Layout/Header';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import DistributionList from '../../../components/List/DistributionList';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import { AuthorizedRouteComponentProps } from 'components/Route';
import useGetDataset from '../../../hooks/useGetDataset';
import MetadataSideBar from 'components/MetadataSideBar';
import MetadataDescription from 'components/MetadataSideBar/MetadataDescription';
import Grid from '@mui/material/Grid';

interface DatasetProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Dataset: React.FC<DatasetProps> = ({ user, embedded, location, match }) => {
    const { isLoading: isLoadingDataset, dataset } = useGetDataset(match.params.dataset);

    const isLoading = isLoadingDataset;
    const breadcrumbs = getBreadCrumbs(location, { dataset });
    const title = dataset ? localizedText(dataset.metadata.title, 'en') : null;

    return (
        <Layout embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {dataset && (
                    <>
                        <Grid container spacing={2}>
                            <Grid size={8}>
                                <MetadataDescription metadata={dataset.metadata} />
                            </Grid>
                            <Grid size={4}>
                                <MetadataSideBar metadata={dataset.metadata} title={title} />
                            </Grid>
                        </Grid>

                        <AssociatedItemsBar items={dataset.count} current="distribution" />

                        <DistributionList
                            dataset={dataset}
                            state={breadcrumbs.current ? breadcrumbs.current.state : null}
                            embedded={embedded}
                        />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default Dataset;
