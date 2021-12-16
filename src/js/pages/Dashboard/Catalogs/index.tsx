import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import {Button, Heading, LoadingOverlay, Pagination, Stack} from "@castoredc/matter";
import {RouteComponentProps} from 'react-router-dom';
import ListItem from "components/ListItem";
import DocumentTitle from "components/DocumentTitle";
import DataGridHelper from "components/DataTable/DataGridHelper";
import {localizedText} from "../../../util";

interface CatalogsProps extends RouteComponentProps<any> {
}

interface CatalogsState {
    catalogs: any,
    isLoading: boolean,
    pagination: any,
}

export default class Catalogs extends Component<CatalogsProps, CatalogsState> {
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
            page : pagination.currentPage,
            perPage : pagination.perPage,
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
                    toast.error(<ToastContent type="error" message="An error occurred while loading your catalogs"/>);
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
        const {history} = this.props;
        const {isLoading, catalogs, pagination} = this.state;

        return <div>
            <DocumentTitle title="Catalogs" />

            {isLoading && <LoadingOverlay accessibleLabel="Loading catalogs"/>}

            <Stack distribution="equalSpacing">
                <Heading type="Section">My catalogs</Heading>

                <Button buttonType="primary" onClick={() => history.push('/dashboard/catalogs/add')}>
                    Add catalog
                </Button>
            </Stack>

            <div>
                {catalogs.map((catalog) => {
                    return <ListItem
                        selectable={false}
                        link={`/dashboard/catalogs/${catalog.slug}`} title={catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : '(no title)'}
                    />
                })}

                {pagination && <Pagination
                    accessibleName="Pagination"
                    onChange={this.handlePagination}
                    pageSize={pagination.perPage}
                    currentPage={pagination.currentPage - 1}
                    totalItems={pagination.totalResults}
                />}
            </div>
        </div>;
    }
}
