import React, { Component } from 'react';
import { classNames, localizedText } from '../../util';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { LoadingOverlay, Pagination } from '@castoredc/matter';
import DataGridHelper from '../DataTable/DataGridHelper';
import ListItem from 'components/ListItem';
import { apiClient } from 'src/js/network';
import { CommonListProps } from 'components/List/types';

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

interface ListProps extends CommonListProps {
    apiEndpoint: string;
    filterFunction?: (item: Item) => boolean;
    noResultsText: string;
}

export default class List extends Component<ListProps, CommonListState> {
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
        const { agent, apiEndpoint, filterFunction } = this.props;

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
                toast.error(<ToastItem type="error" title={message} />);
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
            this.getItems
        );
    };

    render() {
        const { embedded, state, className, visible = true, noResultsText } = this.props;
        const { pagination, items, isLoading } = this.state;

        if (!visible) {
            return null;
        }

        if (items === null) {
            return <LoadingOverlay accessibleLabel="Loading items" content="" />;
        }

        return (
            <div className={classNames('Items', className)}>
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
                            accessibleName="Pagination"
                            onChange={this.handlePagination}
                            pageSize={pagination.perPage}
                            currentPage={pagination.currentPage - 1}
                            totalItems={pagination.totalResults}
                        />
                    </>
                ) : (
                    <div className="NoResults">{noResultsText}</div>
                )}
            </div>
        );
    }
}
