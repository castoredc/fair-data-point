import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import DataSpecificationModulePreview from 'components/DataSpecification/DataSpecificationModulePreview';
import SideTabs from 'components/SideTabs';
import { AuthorizedRouteComponentProps } from 'components/Route';
import PageBody from 'components/Layout/Dashboard/PageBody';
import { apiClient } from '../../../network';
import { getType } from '../../../util';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';
import NoResults from 'components/NoResults';

interface PreviewProps extends AuthorizedRouteComponentProps, ComponentWithNotifications {
    type: string;
    dataSpecification: any;
    version: any;
}

interface PreviewState {
    isLoading: boolean;
    previews: any;
}

class Preview extends Component<PreviewProps, PreviewState> {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            previews: [],
        };
    }

    componentDidMount() {
        this.getPreviews();
    }

    getPreviews = () => {
        const { type, dataSpecification, version, notifications } = this.props;

        this.setState({
            isLoading: true,
        });

        apiClient
            .get('/api/' + type + '/' + dataSpecification.id + '/v/' + version + '/rdf')
            .then(response => {
                this.setState({
                    previews: response.data,
                    isLoading: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    render() {
        const { type } = this.props;
        const { isLoading, previews } = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading preview" />;
        }

        if (previews.modules.length === 0) {
            return <NoResults>This {getType(type)} does not have groups.</NoResults>;
        }

        const tabs = previews.modules.map(element => {
            let icons = [] as any;

            if (element.repeated) {
                icons.push({
                    icon: 'copy',
                    title: 'This group is repeated',
                });
            }

            if (element.dependent) {
                icons.push({
                    icon: 'decision',
                    title: 'This group is dependent',
                });
            }

            return {
                number: element.order,
                title: element.title,
                icons: icons,
                content: (
                    <DataSpecificationModulePreview
                        repeated={element.repeated}
                        dependent={element.dependent}
                        dependencies={element.dependencies}
                        rdf={element.rdf}
                        visualization={element.visualization}
                    />
                ),
            };
        });

        return (
            <PageBody>
                <SideTabs
                    hasTabs
                    title="Groups"
                    tabs={[
                        {
                            title: `Full ${getType(type)}`,
                            content: (
                                <DataSpecificationModulePreview
                                    rdf={previews.full}
                                    visualization={previews.visualization}
                                    repeated={false}
                                    dependent={false}
                                />
                            ),
                        },
                        {
                            type: 'separator',
                        },
                        ...tabs,
                    ]}
                />
            </PageBody>
        );
    }
}

export default withNotifications(Preview);