import React, {Component} from "react";
import axios from "axios";
import {classNames, localizedText} from "../../util";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {Pagination} from "@castoredc/matter";
import InlineLoader from "../LoadingScreen/InlineLoader";
import CatalogListItem from "../ListItem/CatalogListItem";
import DataGridHelper from "../DataTable/DataGridHelper";

export default class CatalogList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingCatalogs: true,
            catalogs:          null,
            pagination: DataGridHelper.getDefaultState(props.embedded ? 5 : 10),
        };
    }

    componentDidMount() {
        this.getCatalogs();
    }

    getCatalogs = () => {
        const {pagination} = this.state;
        const {catalog, study, agent} = this.props;

        this.setState({
            isLoadingCatalogs: true,
        });

        let filters = {
            page:    pagination.currentPage,
            perPage: pagination.perPage,
        };

        let url = '/api/catalog/';

        if(agent) {
            url = '/api/agent/details/' + agent.slug + '/catalog'
        }

        axios.get(url, {params: filters})
            .then((response) => {
                const catalogs = response.data.results.filter((catalog) => {
                    return catalog.hasMetadata
                });

                this.setState({
                    catalogs:          catalogs,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingCatalogs: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalogs: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage:     paginationCount.pageLimit,
            },
        }, () => {
            this.getCatalogs();
        });
    };

    render() {
        const {embedded, pagination, catalogs} = this.state;
        const {visible = true, study, state, className} = this.props;

        if(!visible) {
            return null;
        }

        if (catalogs === null) {
            return <InlineLoader/>;
        }

        return <div className={classNames('Catalogs', className)}>
            {catalogs.length > 0 ? <>
                {/*<div className="Description">*/}
                {/*    Catalogs are collections metadata about resources, such as studies or datasets.*/}
                {/*</div>*/}

                {catalogs.map((catalog) => {
                    if (catalog.hasMetadata === false) {
                        return null;
                    }
                    return <CatalogListItem key={catalog.id}
                                            newWindow={embedded}
                                            state={state}
                                            link={catalog.relativeUrl}
                                            name={localizedText(catalog.metadata.title, 'en')}
                                            description={localizedText(catalog.metadata.description, 'en')}
                    />
                })}

                <Pagination
                    accessibleName="Pagination"
                    onChange={this.handlePagination}
                    pageLimit={pagination.perPage}
                    start={pagination.start}
                    totalItems={pagination.totalResults}
                />

            </> : <div className="NoResults">This {study ? 'study' : 'FAIR Data Point'} does not have any associated catalogs.</div>}
        </div>;
    }
}