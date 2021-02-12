import React, {Component} from "react";
import axios from "axios";
import {paragraphText} from "../../../util";
import MetadataItem from "../../../components/MetadataItem";
import Header from "../../../components/Layout/Header";
import {MethodType, RecruitmentStatus, StudyType} from "../../../components/MetadataItem/EnumMappings";
import Tags from "../../../components/Tags";
import Contacts from "../../../components/MetadataItem/Contacts";
import Organizations from "../../../components/MetadataItem/Organizations";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import DatasetList from "../../../components/List/DatasetList";
import Layout from "../../../components/Layout";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import {Heading} from "@castoredc/matter";

export default class Agent extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading:       true,
            agent:           null,
        };
    }

    componentDidMount() {
        this.getAgent();
    }

    getAgent = () => {
        const { match, type } = this.props;

        axios.get('/api/agent/details/' + type + '/' + match.params.slug)
            .then((response) => {
                this.setState({
                    agent:      response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the details';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const { isLoading, agent } = this.state;
        const { user, embedded } = this.props;

        const title = agent ? agent.name : null;

        return <Layout
            className="Agent"
            title={title}
            isLoading={isLoading}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} title={title} />

            <MainBody isLoading={isLoading}>
                test
            </MainBody>
        </Layout>;
    }
}
