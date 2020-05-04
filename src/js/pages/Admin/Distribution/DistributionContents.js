import React, {Component} from "react";
import axios from "axios/index";

import {Col, Row} from "react-bootstrap";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import {LinkContainer} from "react-router-bootstrap";
import Button from "react-bootstrap/Button";
import AdminPage from "../../../components/AdminPage";
import {toast} from "react-toastify/index";
import ToastContent from "../../../components/ToastContent";
import Icon from "../../../components/Icon";
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

        axios.get('/api/catalog/' + this.props.match.params.catalog + '/dataset/' + this.props.match.params.dataset + '/distribution/' + this.props.match.params.distribution + '/contents')
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const {isLoadingContents, contents} = this.state;
        const {distribution, catalog, dataset} = this.props;

        if (isLoadingContents) {
            return <InlineLoader/>;
        }

        if (distribution.includeAll) {
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
        }

        return <div className="NoResults">This distribution does not have contents.</div>;
    }
}