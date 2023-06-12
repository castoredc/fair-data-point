import React, { Component } from 'react';
import { classNames, localizedText } from '../../util';
import { toast } from 'react-toastify';
import {ToastMessage} from '@castoredc/matter';
import { LoadingOverlay, Pagination } from '@castoredc/matter';
import DataGridHelper from '../DataTable/DataGridHelper';
import ListItem from 'components/ListItem';
import { apiClient } from 'src/js/network';

export default class DistributionList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingDistributions: true,
            distributions: null,
            pagination: DataGridHelper.getDefaultState(props.embedded ? 5 : 10),
        };
    }

    componentDidMount() {
        this.getDistributions();
    }

    getDistributions = () => {
        const { pagination } = this.state;
        const { dataset, agent } = this.props;

        this.setState({
            isLoadingDistributions: true,
        });

        let filters = {
            page: pagination.currentPage,
            perPage: pagination.perPage,
        };

        let url = '/api/distribution/';

        if (agent) {
            url = '/api/agent/details/' + agent.slug + '/distribution';
        } else if (dataset) {
            url = '/api/dataset/' + dataset.slug + '/distribution';
        }

        apiClient
            .get(url, { params: filters })
            .then(response => {
                const distributions = response.data.results.filter(distribution => {
                    return distribution.hasMetadata;
                });

                this.setState({
                    distributions: distributions,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingDistributions: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoadingDistributions: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the distributions';
                toast.error(<ToastMessage type="error" title={message} />);
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
                this.getDistributions();
            }
        );
    };

    render() {
        const { embedded, pagination, distributions } = this.state;
        const { visible = true, study, state, className } = this.props;

        if (!visible) {
            return null;
        }

        if (distributions === null) {
            return <LoadingOverlay accessibleLabel="Loading distributions" content="" />;
        }

        return (
            <div className={classNames('Distributions', className)}>
                {distributions.length > 0 ? (
                    <>
                        {/*<div className="Description">*/}
                        {/*    Distributions represent a specific available form of a dataset. Each dataset might be*/}
                        {/*    available in different forms, these forms might represent different formats of the*/}
                        {/*    dataset*/}
                        {/*    or different endpoints.*/}
                        {/*</div>*/}

                        {distributions.map(distribution => {
                            if (distribution.hasMetadata === false) {
                                return null;
                            }
                            return (
                                <ListItem
                                    key={distribution.id}
                                    title={localizedText(distribution.metadata.title, 'en')}
                                    description={localizedText(distribution.metadata.description, 'en')}
                                    link={distribution.relativeUrl}
                                    state={state}
                                    newWindow={embedded}
                                    smallIcon={(distribution.accessRights === 2 || distribution.accessRights === 3) && 'lock'}
                                />
                            );
                        })}

                        <Pagination
                            accessibleName="Pagination"
                            onChange={this.handlePagination}
                            pageSize={pagination.perPage}
                            currentPage={pagination.currentPage - 1}
                            totalItems={pagination.totalResults}
                        />
                    </>
                ) : (
                    <div className="NoResults">This dataset does not have any associated distributions.</div>
                )}
            </div>
        );
    }
}
