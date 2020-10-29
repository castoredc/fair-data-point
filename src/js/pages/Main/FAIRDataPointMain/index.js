import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import {Heading} from "@castoredc/matter";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";

export default class FAIRDataPointMain extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFDP: true,
            isLoadingCatalogs: true,
            hasLoadedFDP: false,
            hasLoadedCatalogs: false,
            fdp: null,
            catalogs: []
        };
    }

    componentDidMount() {
        this.getFDP();
        this.getCatalogs();
    }

    getFDP = () => {
        axios.get('/api/fdp')
            .then((response) => {
                this.setState({
                    fdp: response.data,
                    isLoadingFDP: false,
                    hasLoadedFDP: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingFDP: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getCatalogs = () => {
        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs: response.data,
                    isLoadingCatalogs: false,
                    hasLoadedCatalogs: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalogs: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { fdp, catalogs, isLoadingFDP, isLoadingCatalogs } = this.state;
        const { user, embedded, location } = this.props;

        const breadcrumbs = getBreadCrumbs(location, {fdp});

        const title = fdp ? localizedText(fdp.metadata.title, 'en') : null;

        return <Layout
            className="FAIRDataPoint"
            title={title}
            embedded={embedded}
            isLoading={(isLoadingFDP || isLoadingCatalogs)}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title} />

            <MainBody isLoading={(isLoadingFDP || isLoadingCatalogs)}>
                <div className="MainCol">
                    {(fdp && fdp.metadata.description && !embedded) &&
                        <div className="InformationDescription">
                            {localizedText(fdp.metadata.description, 'en', true)}
                        </div>}

                    <Heading type="Subsection">Catalogs</Heading>
                    <div className="Description">
                        Catalogs are collections metadata about resources, such as studies or datasets.
                    </div>

                    {catalogs.length > 0 ? catalogs.map((item, index) => {
                        if(item.hasMetadata === false) {
                            return null;
                        }
                        return <ListItem key={index}
                                         newWindow={embedded}
                                         link={item.relativeUrl}
                                         title={localizedText(item.metadata.title, 'en')}
                                         description={localizedText(item.metadata.description, 'en')} />
                    }) : <div className="NoResults">No catalogs found.</div>}
                </div>
            </MainBody>
        </Layout>;
    }
}
