import React, { Component } from 'react';
import { classNames, localizedText } from '../../util';
import Pagination from '@mui/material/Pagination';
import LoadingOverlay from 'components/LoadingOverlay';
import DataGridHelper from '../DataTable/DataGridHelper';
import ListItem from 'components/ListItem';
import { apiClient } from 'src/js/network';
import { CommonListProps } from 'components/List/types';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import { List as MuiList } from '@mui/material';
import NoResults from 'components/NoResults';

export interface Item {
    id: string;
    hasMetadata: boolean;
    metadata: {
        title: string;
        description: string;
    };
    relativeUrl: string;
    legacy?: {
        metadata?: {
            description?: string;
        };
    };
}

export interface PaginationState {
    currentPage: number;
    perPage: number;
    totalResults: number;
}

export interface CommonListState {
    isLoading: boolean;
    items: Item[] | null;
    pagination: PaginationState;
}

interface ListProps extends CommonListProps, ComponentWithNotifications {
    apiEndpoint: string;
    filterFunction?: (item: Item) => boolean;
    noResultsText: string;
}

class List extends Component<ListProps, CommonListState> {
    constructor(props: ListProps) {
        super(props);

        this.state = {
            isLoading: true,
            items: null,
            pagination: DataGridHelper.getDefaultState(props.embedded ? 5 : 10),
        };
    }

    componentDidMount() {
        this.getItems();
    }

    getItems = () => {
        const { pagination } = this.state;
        const { agent, apiEndpoint, filterFunction, notifications } = this.props;

        this.setState({ isLoading: true });

        let filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        apiClient
            .get(apiEndpoint, { params: filters })
            .then(response => {
                let items = response.data.results;
                if (filterFunction) {
                    items = items.filter(filterFunction);
                }

                this.setState({
                    items: items,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({ isLoading: false });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the items';
                notifications.show(message, { variant: 'error' });
            });
    };

    handlePagination = (event: React.ChangeEvent<unknown>, value: number) => {
        const { pagination } = this.state;

        this.setState(
            {
                pagination: {
                    ...pagination,
                    currentPage: value,
                    perPage: pagination.perPage,
                },
            },
            this.getItems,
        );
    };

    render() {
        const { embedded, state, className, visible = true, noResultsText } = this.props;
        const { pagination, items, isLoading } = this.state;

        if (!visible) {
            return null;
        }

        if (items === null) {
            return <LoadingOverlay accessibleLabel="Loading items" />;
        }

        return (
            <MuiList sx={{ width: '100%' }}>
                {items.length > 0 ? (
                    <>
                        {items.map(item => (
                            <ListItem
                                key={item.id}
                                title={localizedText(item.metadata.title, 'en')}
                                description={localizedText(item.metadata.description, 'en')}
                                link={item.relativeUrl}
                                state={state}
                                newWindow={embedded}
                            />
                        ))}
                        <Pagination
                            onChange={this.handlePagination}
                            page={pagination.currentPage - 1}
                            count={pagination.totalResults}
                        />
                    </>
                ) : (
                    <NoResults>{noResultsText}</NoResults>
                )}
            </MuiList>
        );
    }
}

export default withNotifications(List);