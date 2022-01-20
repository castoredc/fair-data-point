import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Heading, LoadingOverlay, Pagination, Separator, Stack, StackItem} from "@castoredc/matter";
import ListItem from "components/ListItem";
import {localizedText} from "../../../util";
import {toRem} from "@castoredc/matter-utils";
import DataGridHelper from "components/DataTable/DataGridHelper";
import DocumentTitle from "components/DocumentTitle";
import BackButton from "components/BackButton";
import {AuthorizedRouteComponentProps} from "components/Route";

interface SelectCatalogProps extends AuthorizedRouteComponentProps {
}

interface SelectCatalogState {
    catalogs: any,
    isLoading: boolean,
    pagination: any,
}

export default class SelectCatalog extends Component<SelectCatalogProps, SelectCatalogState> {
    constructor(props) {
        super(props);

        this.state = {
            catalogs: [],
            isLoading: false,
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    getCatalogs = () => {
        const {pagination} = this.state;

        this.setState({
            isLoading: true,
        });

        let filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
            acceptSubmissions: true,
        };

        axios.get('/api/catalog', {params: filters})
            .then((response) => {
                this.setState({
                    catalogs: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
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
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage + 1,
                perPage: paginationCount.pageLimit,
            },
        }, () => {
            this.getCatalogs();
        });
    };

    componentDidMount() {
        this.getCatalogs();
    }

    render() {
        const {isLoading, catalogs, pagination} = this.state;

        return <div style={{marginLeft: 'auto', marginRight: 'auto'}}>
            <DocumentTitle title="Add a study"/>

            {isLoading && <LoadingOverlay accessibleLabel="Loading catalogs"/>}

            <Stack distribution="center">
                <StackItem style={{width: toRem(480), marginTop: '3.2rem'}}>
                    <BackButton to="/dashboard/studies">Back to studies</BackButton>

                    <Heading type="Section">Choose a Catalog</Heading>

                    <p>
                        Please choose a catalog where you would like to add your study to.
                    </p>

                    <Separator/>

                    {catalogs.length > 0 ? catalogs.map((catalog) => {
                            return <ListItem key={catalog.id}
                                             title={catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : '(no title)'}
                                             link={`/dashboard/studies/add/${catalog.slug}`}
                                             customIcon="catalog"
                            />
                        },
                    ) : <div className="NoResults">No catalogs found.</div>}

                    {pagination && <Pagination
                        accessibleName="Pagination"
                        onChange={this.handlePagination}
                        pageSize={pagination.perPage}
                        currentPage={pagination.currentPage - 1}
                        totalItems={pagination.totalResults}
                    />}

                </StackItem>
            </Stack>
        </div>;
    }
}
