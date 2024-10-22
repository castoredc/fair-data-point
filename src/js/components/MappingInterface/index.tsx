import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { LoadingOverlay } from '@castoredc/matter';
import ModuleMappingInterface from './ModuleMappingInterface';
import NodeMappingInterface from './NodeMappingInterface';
import { apiClient } from 'src/js/network';

interface MappingInterfaceProps {
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

export default class MappingInterface extends Component<MappingInterfaceProps, MappingInterfaceState> {
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
        const { studyId } = this.props;

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
                    toast.error(<ToastItem type="error" title={error.response.data.error} />);
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
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
