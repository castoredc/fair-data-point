import React, { Component } from 'react';
import { toast } from 'react-toastify';
import ToastItem from 'components/ToastItem';
import { LoadingOverlay } from '@castoredc/matter';
import DistributionContentsDependencyEditor from 'components/DependencyEditor/DistributionContentsDependencyEditor';
import { formatQuery } from 'react-querybuilder';
import { apiClient } from 'src/js/network';

interface DistributionSubsetProps {
    distribution: any;
    dataset: string;
    match: {
        params: {
            dataset: string;
            distribution: string;
        };
    };
}

interface DistributionSubsetState {
    isLoadingNodes: boolean;
    isLoadingDataModel: boolean;
    isLoadingInstitutes: boolean;
    isLoadingContents: boolean;
    nodes: any | null;
    dataModel: any | null;
    institutes: any[];
    contents: any | null;
    query: any;
    isLoading?: boolean;
    submitDisabled?: boolean;
    validation?: any;
}

export default class DistributionSubset extends Component<DistributionSubsetProps, DistributionSubsetState> {
    constructor(props: DistributionSubsetProps) {
        super(props);
        this.state = {
            isLoadingNodes: props.distribution.type === 'rdf',
            isLoadingDataModel: props.distribution.type === 'rdf',
            isLoadingInstitutes: true,
            isLoadingContents: true,
            nodes: null,
            dataModel: null,
            institutes: [],
            contents: null,
            query: '',
        };
    }

    componentDidMount() {
        const { distribution } = this.props;

        if (distribution.type === 'rdf') {
            this.getDataModel();
            this.getNodes();
        }

        this.getInstitutes();
        this.getContents();
    }

    getNodes = () => {
        const { distribution } = this.props;

        this.setState({ isLoadingNodes: true });

        apiClient
            .get(`/api/data-model/${distribution.dataModel.dataModel}/v/${distribution.dataModel.id}/node`)
            .then(response => {
                this.setState({ nodes: response.data, isLoadingNodes: false });
            })
            .catch(error => {
                this.setState({ isLoadingNodes: false });
                const message =
                    error.response?.data?.error || 'An error occurred while loading the nodes';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getDataModel = () => {
        const { distribution } = this.props;

        this.setState({ isLoadingDataModel: true });

        apiClient
            .get(`/api/data-model/${distribution.dataModel.dataModel}`)
            .then(response => {
                this.setState({
                    dataModel: response.data,
                    isLoadingDataModel: false,
                });
            })
            .catch(error => {
                this.setState({ isLoadingDataModel: false });
                const message =
                    error.response?.data?.error || 'An error occurred while loading the data model';
                toast.error(<ToastItem type="error" title={message} />);
            });
    };

    getInstitutes = () => {
        const { distribution } = this.props;

        this.setState({ isLoadingInstitutes: true });

        apiClient
            .get(`/api/castor/study/${distribution.study.id}/institutes/`)
            .then(response => {
                this.setState({ institutes: response.data, isLoadingInstitutes: false });
            })
            .catch(error => {
                const message =
                    error.response?.data?.error || 'An error occurred';
                toast.error(<ToastItem type="error" title={message} />);
                this.setState({ isLoadingInstitutes: false });
            });
    };

    getContents = () => {
        this.setState({ isLoadingContents: true });

        apiClient
            .get(`/api/dataset/${this.props.match.params.dataset}/distribution/${this.props.match.params.distribution}/contents`)
            .then(response => {
                this.setState({ contents: response.data, isLoadingContents: false });
            })
            .catch(error => {
                const message =
                    error.response?.data?.error || 'An error occurred while loading the distribution';
                toast.error(<ToastItem type="error" title={message} />);
                this.setState({ isLoadingContents: false });
            });
    };

    handleChange = (query: any) => {
        this.setState({ query });
    };

    handleSave = () => {
        const { query } = this.state;
        const { dataset, distribution } = this.props;

        const sqlQuery = formatQuery(query, 'sql') as string;
        const replaced = sqlQuery.replace(/\(|\)|and|or| /gi, '');

        this.setState({ isLoading: true, submitDisabled: true });

        apiClient
            .post(`/api/dataset/${dataset}/distribution/${distribution.slug}/subset`, {
                dependencies: replaced.length === 0 ? null : query,
            })
            .then(() => {
                this.setState({ isLoading: false, submitDisabled: false });
                this.getContents();
                toast.success(<ToastItem type="success" title="The subset details are saved successfully" />, {
                    position: 'top-right',
                });
            })
            .catch(error => {
                if (error.response?.status === 400) {
                    this.setState({ validation: error.response.data.fields });
                } else {
                    toast.error(<ToastItem type="error" title="An error occurred" />);
                }
                this.setState({ submitDisabled: false, isLoading: false });
            });
    };

    render() {
        const { isLoadingNodes, isLoadingDataModel, isLoadingInstitutes, isLoadingContents, nodes, institutes, contents } = this.state;
        const { distribution } = this.props;

        if (isLoadingInstitutes || isLoadingContents) {
            return <LoadingOverlay accessibleLabel="Loading distribution" />;
        }

        return (
            <div className="PageContainer">
                <DistributionContentsDependencyEditor
                    prefixes={[]}
                    institutes={institutes}
                    handleChange={this.handleChange}
                    value={contents?.dependencies}
                    type={distribution.type}
                    valueNodes={distribution.type === 'rdf' ? nodes?.value : null}
                    save={this.handleSave}
                />
            </div>
        );
    }
}