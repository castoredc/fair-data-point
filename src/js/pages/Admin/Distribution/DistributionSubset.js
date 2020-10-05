import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DistributionContentsDependencyEditor
    from "../../../components/DependencyEditor/DistributionContentsDependencyEditor";
import {formatQuery} from "react-querybuilder";
import ModuleDependencyEditor from "../../../components/DependencyEditor/ModuleDependencyEditor";

export default class DistributionSubset extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingNodes: true,
            isLoadingDataModel: true,
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
        this.getDataModel();
        this.getNodes();
        this.getInstitutes();
        this.getContents();
    }

    getNodes = () => {
        const {distribution} = this.props;

        this.setState({
            isLoadingNodes: true,
        });

        axios.get('/api/model/' + distribution.dataModel.dataModel + '/v/' + distribution.dataModel.id + '/node')
            .then((response) => {
                this.setState({
                    nodes: response.data,
                    isLoadingNodes: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingNodes: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the nodes';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getDataModel = () => {
        const {distribution} = this.props;

        this.setState({
            isLoadingDataModel: true,
        });

        axios.get('/api/model/' + distribution.dataModel.dataModel)
            .then((response) => {
                this.setState({
                    dataModel: response.data,
                    isLoadingDataModel: false,
                    currentVersion: distribution.dataModel.id,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataModel: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data model';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getInstitutes = () => {
        const {distribution} = this.props;

        this.setState({
            isLoadingInstitutes: true,
        });

        axios.get('/api/castor/study/' + distribution.study.id + '/institutes/')
            .then((response) => {
                this.setState({
                    institutes: response.data,
                    isLoadingInstitutes: false,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingInstitutes: false,
                });
            });
    };

    getContents = () => {
        this.setState({
            isLoadingContents: true,
        });

        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution + '/contents')
            .then((response) => {
                this.setState({
                    contents:          response.data,
                    isLoadingContents: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingContents: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handleChange = (query) => {
        this.setState({
            query: query,
        });
    };

    handleSave = () => {
        const {query} = this.state;
        const {dataset, distribution} = this.props;

        const sqlQuery = formatQuery(query, 'sql');
        const replaced = sqlQuery.replace(/\(|\)|and|or| /ig, '');

        this.setState({
            isLoading:      true,
            submitDisabled: true,
        });

        axios.post('/api/dataset/' + dataset + '/distribution/' + distribution.slug + '/subset', {
            dependencies: replaced.length === 0 ? null : query
        })
            .then(() => {
                this.setState({
                    isLoading:      false,
                    submitDisabled: false,
                });

                this.getContents();

                toast.success(<ToastContent type="success"
                                            message="The subset details are saved successfully"/>, {
                    position: "top-right",
                });
            })
            .catch((error) => {
                if (error.response && error.response.status === 400) {
                    this.setState({
                        validation: error.response.data.fields,
                    });
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>, {
                        position: "top-center",
                    });
                }
                this.setState({
                    submitDisabled: false,
                    isLoading:      false,
                });
            });
    };

    render() {
        const {isLoadingNodes, isLoadingDataModel, isLoadingInstitutes, isLoadingContents, nodes, institutes, contents} = this.state;
        const {distribution} = this.props;

        if (isLoadingNodes || isLoadingDataModel || isLoadingInstitutes || isLoadingContents) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <DistributionContentsDependencyEditor
                type={distribution.type}
                institutes={institutes}
                handleChange={this.handleChange}
                value={contents.dependencies}
                valueNodes={distribution.type === 'rdf' ? nodes.value : null}
                save={this.handleSave}
            />
        </div>;
    }
}