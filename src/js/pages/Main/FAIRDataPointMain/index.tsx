import React from 'react';
import Layout from '../../../components/Layout';
import Header from '../../../components/Layout/Header';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import CatalogList from '../../../components/List/CatalogList';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import useGetFdp from '../../../hooks/useGetFdp';
import { AuthorizedRouteComponentProps } from 'components/Route';
import MetadataSideBar from 'components/MetadataSideBar';
import MetadataDescription from 'components/MetadataSideBar/MetadataDescription';
import { localizedText } from '../../../util';
import Grid from '@mui/material/Grid';

interface FAIRDataPointMainProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const FAIRDataPointMain: React.FC<FAIRDataPointMainProps> = ({ user, embedded, location }) => {
    const { isLoading: isLoadingFdp, fdp } = useGetFdp();

    const isLoading = isLoadingFdp;
    const breadcrumbs = getBreadCrumbs(location, { fdp });
    const title = fdp ? localizedText(fdp.metadata.title, 'en') : null;

    return (
        <Layout className="FAIRDataPoint" embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {fdp && (
                    <>
                        <Grid container spacing={2}>
                            <Grid size={8}>
                                <MetadataDescription metadata={fdp.metadata} />
                            </Grid>
                            <Grid size={4}>
                                <MetadataSideBar metadata={fdp.metadata} title={title} />
                            </Grid>
                        </Grid>

                        <AssociatedItemsBar items={fdp.count} current="catalog" />

                        <CatalogList embedded={embedded} />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default FAIRDataPointMain;
