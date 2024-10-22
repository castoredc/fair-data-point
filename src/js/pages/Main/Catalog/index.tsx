import React, { useState } from 'react';
import Layout from '../../../components/Layout';
import Header from '../../../components/Layout/Header';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import DatasetList from '../../../components/List/DatasetList';
import useGetFdp from '../../../hooks/useGetFdp';
import useGetCatalog from '../../../hooks/useGetCatalog';
import { AuthorizedRouteComponentProps } from 'components/Route';
import MetadataSideBar from 'components/MetadataSideBar';
import MetadataDescription from 'components/MetadataSideBar/MetadataDescription';
import StudyList from 'components/List/StudyList';
import { localizedText } from '../../../util';

interface CatalogProps extends AuthorizedRouteComponentProps {
    embedded: boolean;
}

const Catalog: React.FC<CatalogProps> = ({ user, embedded, location, match }) => {
    const [currentItem, setCurrentItem] = useState('study');
    const { isLoading: isLoadingFdp, fdp } = useGetFdp();
    const { isLoading: isLoadingCatalog, catalog } = useGetCatalog(match.params.catalog);

    const isLoading = isLoadingFdp || isLoadingCatalog;
    const breadcrumbs = getBreadCrumbs(location, { fdp, catalog });
    const title = catalog ? localizedText(catalog.metadata.title, 'en') : null;

    return (
        <Layout className="Catalog" embedded={embedded}>
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={isLoading}>
                {catalog && (
                    <>
                        <div className="MainCol">
                            <MetadataDescription metadata={catalog.metadata} />
                        </div>
                        <div className="SideCol">
                            <MetadataSideBar
                                metadata={catalog.metadata}
                                title={title}
                            />
                        </div>

                        <AssociatedItemsBar items={catalog.count} current={currentItem} onClick={setCurrentItem} />

                        <StudyList
                            visible={currentItem === 'study'}
                            catalog={catalog}
                            state={breadcrumbs.current ? breadcrumbs.current.state : null}
                            embedded={embedded}
                            className="MainCol"
                        />

                        <DatasetList
                            visible={currentItem === 'dataset'}
                            catalog={catalog}
                            state={breadcrumbs.current ? breadcrumbs.current.state : null}
                            embedded={embedded}
                            className="MainCol"
                        />
                    </>
                )}
            </MainBody>
        </Layout>
    );
};

export default Catalog;
