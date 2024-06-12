import React from 'react';
import Layout from '../../../components/Layout';
import Header from '../../../components/Layout/Header';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import CatalogList from '../../../components/List/CatalogList';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import useJsonLdRepresentation from '../../../hooks/useJsonLdRepresentation';
import { localizedText, titleAndDescriptionContext } from 'utils/jsonLdUtils';
import useGetFdp from '../../../hooks/useGetFdp';
import { AuthorizedRouteComponentProps } from 'components/Route';
import MetadataSideBar from 'components/MetadataSideBar';
import MetadataDescription from 'components/MetadataSideBar/MetadataDescription';

interface FAIRDataPointMainProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const FAIRDataPointMain: React.FC<FAIRDataPointMainProps> = ({ user, embedded, location }) => {
    const { isLoading: isLoadingFdp, fdp } = useGetFdp();
    const { data, isLoading: isLoadingJsonLd } = useJsonLdRepresentation('/fdp?format=jsonld', titleAndDescriptionContext);

    const isLoading = isLoadingFdp || isLoadingJsonLd;
    const breadcrumbs = getBreadCrumbs(location, { fdp });
    const title = localizedText(data.title, 'en');

    return (
        <Layout className="FAIRDataPoint" title={title} embedded={embedded} isLoading={isLoading}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {fdp && (
                    <>
                        {data.description && !embedded && (
                            <>
                                <div className="MainCol">
                                    <MetadataDescription metadata={fdp.metadata} />
                                </div>
                                <div className="SideCol">
                                    <MetadataSideBar
                                        metadata={fdp.metadata}
                                        title={title}
                                    />
                                </div>
                            </>
                        )}

                        <AssociatedItemsBar items={fdp.count} current="catalog" />

                        <CatalogList embedded={embedded} className="MainCol" />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default FAIRDataPointMain;