import React, {Component} from "react";
import axios from "axios/index";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import CSVStudyStructure from "../../../components/StudyStructure/CSVStudyStructure";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";

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

        if (distribution.includeAllData) {
            return <div className="NoResults">This distribution contains all fields.</div>;
        }

        if (distribution.type === 'csv') {
            return <CSVStudyStructure
                studyId={distribution.studyId}
                distributionContents={contents}
                catalog={catalog}
                dataset={dataset}
                distribution={distribution.slug}
            />;
        } else if (distribution.type === 'rdf') {
            return null;
        }

        return <div className="NoResults">This distribution does not have contents.</div>;
    }
}