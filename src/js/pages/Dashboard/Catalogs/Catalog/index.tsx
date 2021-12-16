import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../../util";
import {Route, RouteComponentProps, Switch} from 'react-router-dom';
import {LoadingOverlay} from "@castoredc/matter";
import DocumentTitle from "components/DocumentTitle";
import SideBar from "components/SideBar";
import NotFound from "pages/NotFound";
import {toast} from "react-toastify";
import ToastContent from "components/ToastContent";
import CatalogMetadataForm from "components/Form/Metadata/CatalogMetadataForm";
import CatalogForm from "components/Form/Admin/CatalogForm";
import AddStudy from "pages/Dashboard/Catalogs/Catalog/AddStudy";
import Studies from "pages/Dashboard/Catalogs/Catalog/Studies";
import Datasets from "pages/Dashboard/Catalogs/Catalog/Datasets";
import AddDataset from "pages/Dashboard/Catalogs/Catalog/AddDataset";
import Body from "components/Layout/Dashboard/Body";
import Header from "components/Layout/Dashboard/Header";

interface CatalogProps extends RouteComponentProps<any> {
}

interface CatalogState {
    catalog: any,
    isLoading: boolean,
}

export default class Catalog extends Component<CatalogProps, CatalogState> {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            catalog: null,
        };
    }

    componentDidMount() {
        this.getCatalog();
    }

    getCatalog = () => {
        this.setState({
            isLoading: true,
        });

        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog: response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred while loading the catalog"/>);
                }
            });
    };

    render() {
        const {catalog, isLoading} = this.state;
        const {location} = this.props;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading catalog"/>;
        }

        const title = catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : null;

        return <>
            <DocumentTitle title={title}/>

            <SideBar
                back={{
                    to: '/dashboard/catalogs',
                    title: 'Back to catalog list'
                }}
                location={location}
                items={[
                    {
                        to: '/dashboard/catalogs/' + catalog.slug,
                        exact: true,
                        title: 'Catalog',
                        customIcon: 'catalog'
                    },
                    {
                        to: '/dashboard/catalogs/' + catalog.slug + '/metadata',
                        exact: true,
                        title: 'Metadata',
                        customIcon: 'metadata'
                    },
                    {
                        type: 'separator'
                    },
                    {
                        to: '/dashboard/catalogs/' + catalog.slug + '/datasets',
                        exact: true,
                        title: 'Datasets',
                        customIcon: 'dataset'
                    },
                    {
                        to: '/dashboard/catalogs/' + catalog.slug + '/studies',
                        exact: true,
                        title: 'Studies',
                        icon: 'study'
                    }
                ]}
            />

            <Body>
                <Header title={title}/>

                <Switch>
                    <Route path="/dashboard/catalogs/:catalog" exact
                           render={(props) => <div>
                               <CatalogForm
                                   catalog={catalog}
                               />
                           </div>}/>
                    <Route path="/dashboard/catalogs/:catalog/metadata" exact
                           render={(props) => <div>
                               <CatalogMetadataForm catalog={catalog} onSave={this.getCatalog}/>
                           </div>}/>
                    <Route path="/dashboard/catalogs/:catalog/studies/add" exact
                           render={(props) => <AddStudy {...props} catalog={catalog.slug}/>}/>
                    <Route path="/dashboard/catalogs/:catalog/studies" exact
                           render={(props) => <Studies {...props} catalog={catalog.slug}/>}/>
                    <Route path="/dashboard/catalogs/:catalog/datasets" exact
                           render={(props) => <Datasets {...props} catalog={catalog.slug}/>}/>
                    <Route path="/dashboard/catalogs/:catalog/datasets/add" exact
                           render={(props) => <AddDataset {...props} catalog={catalog.slug}/>}/>
                    <Route component={NotFound}/>
                </Switch>
            </Body>
        </>;
    }
}