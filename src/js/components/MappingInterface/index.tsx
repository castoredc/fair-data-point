import React, { Component } from 'react';
import LoadingOverlay from 'components/LoadingOverlay';
import ModuleMappingInterface from './ModuleMappingInterface';
import NodeMappingInterface from './NodeMappingInterface';
import { apiClient } from 'src/js/network';
import withNotifications, { ComponentWithNotifications } from 'components/WithNotifications';

interface MappingInterfaceProps extends ComponentWithNotifications {
    studyId: string;
    dataset: any;
    distribution: any;
    versionId: string;
    mapping: any;
    type: 'node' | 'module';
    onSave: () => void;
}

interface MappingInterfaceState {
    isLoading: boolean;
    isLoadingStructure: boolean;
    structure: any | null;
}

class MappingInterface extends Component<MappingInterfaceProps, MappingInterfaceState> {
    constructor(props: MappingInterfaceProps) {
        super(props);
        this.state = {
            isLoading: false,
            isLoadingStructure: true,
            structure: null,
        };
    }

    componentDidMount() {
        this.getStructure();
    }

    getStructure = () => {
        const { studyId, notifications } = this.props;

        apiClient
            .get('/api/castor/study/' + studyId + '/structure')
            .then(response => {
                this.setState({
                    structure: response.data,
                    isLoadingStructure: false,
                });
            })
            .catch(error => {
                if (error.response && typeof error.response.data.error !== 'undefined') {
                    notifications.show(error.response.data.error, { variant: 'error' });
                } else {
                    notifications.show('An error occurred', { variant: 'error' });
                }

                this.setState({
                    isLoadingStructure: false,
                });
            });
    };

    render() {
        const { studyId, dataset, distribution, versionId, mapping, type, onSave } = this.props;
        const { structure, isLoadingStructure } = this.state;

        return (
            <div className="MappingInterface">
                {isLoadingStructure && <LoadingOverlay accessibleLabel="Loading structure" />}

                {structure && mapping && type === 'node' && (
                    <NodeMappingInterface
                        studyId={studyId}
                        mapping={mapping}
                        dataset={dataset}
                        distribution={distribution}
                        versionId={versionId}
                        onSave={onSave}
                    />
                )}

                {structure && mapping && type === 'module' && (
                    <ModuleMappingInterface
                        mapping={mapping}
                        structure={structure}
                        dataset={dataset}
                        distribution={distribution}
                        versionId={versionId}
                        onSave={onSave}
                    />
                )}
            </div>
        );
    }
}

export default withNotifications(MappingInterface);