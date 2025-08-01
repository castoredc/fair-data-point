import React, { Component } from 'react';
import Header from '../../../components/Layout/Header';
import Layout from '../../../components/Layout';
import MainBody from '../../../components/Layout/MainBody';
import { getBreadCrumbs } from '../../../utils/BreadcrumbUtils';
import AssociatedItemsBar from '../../../components/AssociatedItemsBar';
import DatasetList from '../../../components/List/DatasetList';
import CatalogList from '../../../components/List/CatalogList';
import DistributionList from '../../../components/List/DistributionList';
import { apiClient } from 'src/js/network';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { GenericAgentType } from 'types/AgentListType';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface AgentProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    embedded: boolean;
    type: string;
}

interface AgentState {
    isLoading: boolean;
    agent: AgentData | null;
    currentItem: string | null;
}

interface AgentData extends GenericAgentType {
    slug: string;
    count: {
        [key: string]: number;
    };
}

class Agent extends Component<AgentProps, AgentState> {
    constructor(props: AgentProps) {
        super(props);
        this.state = {
            isLoading: true,
            agent: null,
            currentItem: null,
        };
    }

    componentDidMount() {
        this.getAgent();
    }

    getAgent = () => {
        const { match, notifications } = this.props;

        apiClient
            .get(`/api/agent/details/${match.params.slug}`)
            .then(response => {
                this.setState({
                    agent: response.data,
                    currentItem: Object.keys(response.data.count).find(key => response.data.count[key] > 0) ?? null,
                    isLoading: false,
                });
            })
            .catch(error => {
                this.setState({
                    isLoading: false,
                });

                const message =
                    error.response && typeof error.response.data.error !== 'undefined'
                        ? error.response.data.error
                        : 'An error occurred while loading the details';
                notifications.show(message, { variant: 'error' });
            });
    };

    handleItemChange = (item: string) => {
        this.setState({
            currentItem: item,
        });
    };

    render() {
        const { isLoading, agent, currentItem } = this.state;
        const { user, embedded, location } = this.props;

        const title = agent ? agent.name : '';

        const breadcrumbs = getBreadCrumbs(location, { agent });

        return (
            <Layout embedded={embedded}>
                <Header user={user} embedded={embedded} title={title} />

                <MainBody isLoading={isLoading}>
                    {agent && currentItem && (
                        <>
                            <AssociatedItemsBar items={agent.count} current={currentItem}
                                                onClick={this.handleItemChange} />

                            <CatalogList
                                visible={currentItem === 'catalog'}
                                agent={agent}
                                state={breadcrumbs.current ? breadcrumbs.current.state : null}
                                embedded={embedded}
                            />

                            <DatasetList
                                visible={currentItem === 'dataset'}
                                agent={agent}
                                state={breadcrumbs.current ? breadcrumbs.current.state : null}
                                embedded={embedded}
                            />

                            <DistributionList
                                visible={currentItem === 'distribution'}
                                agent={agent}
                                state={breadcrumbs.current ? breadcrumbs.current.state : null}
                                embedded={embedded}
                            />
                        </>
                    )}
                </MainBody>
            </Layout>
        );
    }
}

export default withNotifications(Agent);