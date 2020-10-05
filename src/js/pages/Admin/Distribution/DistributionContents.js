import React, {Component} from "react";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DistributionContentsRdf from "./DistributionContentsRdf";
import DistributionContentsCsv from "./DistributionContentsCsv";

export default class DistributionContents extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingContents:     true,
            hasLoadedContents:     false,
            contents:              null,
        };
    }

    componentDidMount() {
        this.getContents();
    }

    getContents = () => {
        this.setState({
            isLoadingContents: true,
        });

        axios.get('/api/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution + '/contents')
            .then((response) => {
                this.setState({
                    contents:          response.data.elements,
                    isLoadingContents: false,
                    hasLoadedContents: true,
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

    render() {
        const {isLoadingContents, contents} = this.state;
        const {distribution, catalog, dataset} = this.props;

        if (isLoadingContents) {
            return <InlineLoader/>;
        }

        if (distribution.type === 'csv') {
            return <DistributionContentsCsv contents={contents} catalog={catalog} distribution={distribution} dataset={dataset} />;
        } else if (distribution.type === 'rdf') {
            return <DistributionContentsRdf dataset={dataset} distribution={distribution} />;
        }

        return <div className="NoResults">This distribution does not have contents.</div>;
    }
}