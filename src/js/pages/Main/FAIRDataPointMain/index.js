import React, { Component } from 'react';
import { localizedText } from '../../../util';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import Layout from '../../../components/Layout';
import Header from '../../../components/Layout/Header';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import MetadataSideBar from '../../../components/MetadataSideBar';
import CatalogList from '../../../components/List/CatalogList';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import { apiClient } from 'src/js/network';

export default class FAIRDataPointMain extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFDP: true,
            hasLoadedFDP: false,
            fdp: null,
        };
    }

    componentDidMount() {
        this.getFDP();
    }

    getFDP = () => {
        apiClient
            .get('/api/fdp')
            .then(response => {
                this.setState({
                    fdp: response.data,
                    isLoadingFDP: false,
                    hasLoadedFDP: true,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingFDP: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    render() {
        const { fdp, catalogs, isLoadingFDP, isLoadingCatalogs } = this.state;
        const { user, embedded, location } = this.props;

        const breadcrumbs = getBreadCrumbs(location, { fdp });

        const title = fdp ? localizedText(fdp.metadata.title, 'en') : null;

        return (
            <Layout className="FAIRDataPoint" title={title} embedded={embedded} isLoading={isLoadingFDP || isLoadingCatalogs}>
                <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

                <MainBody isLoading={isLoadingFDP || isLoadingCatalogs}>
                    {fdp && (
                        <>
                            {fdp.metadata.description && !embedded && (
                                <>
                                    <div className="MainCol">
                                        <div className="InformationDescription">{localizedText(fdp.metadata.description, 'en', true)}</div>
                                    </div>
                                    <div className="SideCol">
                                        <MetadataSideBar type="fdp" metadata={fdp.metadata} name={title} />
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
    }
}
