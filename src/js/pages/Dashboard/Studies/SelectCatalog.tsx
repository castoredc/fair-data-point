import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { LoadingOverlay, Pagination } from '@castoredc/matter';
import ListItem from 'components/ListItem';
import { localizedText } from '../../../util';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import SelectPage from 'components/SelectPage';

interface SelectCatalogProps extends AuthorizedRouteComponentProps {}

interface SelectCatalogState {
    catalogs: any;
    isLoading: boolean;
    pagination: any;
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
        const { pagination } = this.state;

        this.setState({
            isLoading: true,
        });

        let filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
            acceptSubmissions: true,
        };

        apiClient
            .get('/api/catalog/my', { params: filters })
            .then(response => {
                this.setState({
                    catalogs: response.data.results,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                if (error.response && typeof error.response.data.error !== 'undefined') {
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
            });
    };

    handlePagination = paginationCount => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: paginationCount.currentPage + 1,
                    perPage: paginationCount.pageSize,
                },
            },
            () => {
                this.getCatalogs();
            }
        );
    };

    componentDidMount() {
        this.getCatalogs();
    }

    render() {
        const { isLoading, catalogs, pagination } = this.state;
        const { history } = this.props;

        return (
            <SelectPage
                title="Choose a catalog"
                description="Please choose a catalog where you would like to add your study to."
                backButton={{
                    to: '/dashboard/studies',
                    label: 'Back to studies',
                }}
                history={history}
            >
                {isLoading && <LoadingOverlay accessibleLabel="Loading catalogs" />}

                {catalogs.length > 0 ? (
                    catalogs.map(catalog => {
                        return (
                            <ListItem
                                key={catalog.id}
                                title={catalog.hasMetadata ? localizedText(catalog.metadata.title, 'en') : '(no title)'}
                                link={`/dashboard/studies/add/${catalog.slug}`}
                                customIcon="catalog"
                            />
                        );
                    })
                ) : (
                    <div className="NoResults">No catalogs found.</div>
                )}

                {pagination && (
                    <Pagination
                        accessibleName="Pagination"
                        onChange={this.handlePagination}
                        pageSize={pagination.perPage}
                        currentPage={pagination.currentPage - 1}
                        totalItems={pagination.totalResults}
                    />
                )}
            </SelectPage>
        );
    }
}
