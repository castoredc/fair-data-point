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
                        <div className="MainCol">
                            <MetadataDescription metadata={fdp.metadata} />
                        </div>
                        <div className="SideCol">
                            <MetadataSideBar metadata={fdp.metadata} title={title} />
                        </div>

                        <AssociatedItemsBar items={fdp.count} current="catalog" />

                        <CatalogList embedded={embedded} className="MainCol" />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default FAIRDataPointMain;
