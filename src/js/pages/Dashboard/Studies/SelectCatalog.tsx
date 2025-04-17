import React, { Component } from 'react';
import Pagination from '@mui/material/Pagination';
import LoadingOverlay from 'components/LoadingOverlay';
import ListItem from 'components/ListItem';
import { localizedText } from '../../../util';
import DataGridHelper from 'components/DataTable/DataGridHelper';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { apiClient } from 'src/js/network';
import SelectPage from 'components/SelectPage';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface SelectCatalogProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
}

interface SelectCatalogState {
    catalogs: any;
    isLoading: boolean;
    pagination: any;
}

class SelectCatalog extends Component<SelectCatalogProps, SelectCatalogState> {
    constructor(props) {
        super(props);

        this.state = {
            catalogs: [],
            isLoading: false,
            pagination: DataGridHelper.getDefaultState(25),
        };
    }

    getCatalogs = () => {
        const { notifications } = this.props;
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
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }
            });
    };

    handlePagination = (event: React.ChangeEvent<unknown>, value: number) => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: value,
                    perPage: pagination.pageSize,
                },
            },
            () => {
                this.getCatalogs();
            },
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
                                icon="catalog"
                            />
                        );
                    })
                ) : (
                    <div className="NoResults">No catalogs found.</div>
                )}

                {pagination && (
                    <Pagination
                        onChange={this.handlePagination}
                        page={pagination.currentPage - 1}
                        count={pagination.totalResults}
                    />
                )}
            </SelectPage>
        );
    }
}

export default withNotifications(SelectCatalog);