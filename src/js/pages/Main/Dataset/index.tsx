import React, { Component, useMemo } from 'react';
import { localizedText } from '../../../util';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import Layout from '../../../components/Layout';
import Header from '../../../components/Layout/Header';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import LegacyMetadataSideBar from '../../../components/MetadataSideBar/LegacyMetadataSideBar';
import DistributionList from '../../../components/List/DistributionList';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import { apiClient } from 'src/js/network';
import { AuthorizedRouteComponentProps } from 'components/Route';
import useGetCatalog from '../../../hooks/useGetCatalog';
import useJsonLdRepresentation from '../../../hooks/useJsonLdRepresentation';
import useGetDataset from '../../../hooks/useGetDataset';
import { titleAndDescriptionContext } from 'utils/jsonLdUtils';

interface DatasetProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Dataset: React.FC<DatasetProps> = ({ user, embedded, location, match }) => {
    const { isLoading: isLoadingDataset, dataset } = useGetDataset(match.params.dataset);
    const { data, isLoading: isLoadingJsonLd } = useJsonLdRepresentation(`${window.location.pathname}?format=jsonld`, titleAndDescriptionContext);

    const isLoading = isLoadingDataset || isLoadingJsonLd;
    const breadcrumbs = getBreadCrumbs(location, { dataset });
    const title = dataset ? localizedText(dataset.metadata.title, 'en') : null;

    return (
        <Layout className="Dataset" title={title} isLoading={isLoading} embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {dataset && (
                    <>
                        <div className="MainCol">
                            {data.description && !embedded && (
                                <div className="InformationDescription">{localizedText(data.description, 'en', true)}</div>
                            )}
                        </div>

                        <div className="SideCol">
                            <LegacyMetadataSideBar type="dataset" metadata={dataset.metadata} name={title} />
                        </div>

                        <AssociatedItemsBar items={dataset.count} current="distribution" />

                        <DistributionList dataset={dataset} embedded={embedded} className="MainCol" />
                    </>
                )}
            </MainBody>
        </Layout>
    );
}

export default Dataset;