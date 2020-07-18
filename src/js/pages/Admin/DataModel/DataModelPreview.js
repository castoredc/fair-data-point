import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import ToastContent from "../../../components/ToastContent";
import Highlight from "../../../components/Highlight";
import SideTabs from "../../../components/SideTabs";
import DataModelModulePreview from "../../../components/DataModelModule/DataModelModulePreview";

export default class DataModelPreview extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingPreviews:     true,
            hasLoadedPreviews:     false,
            previews:              [],
        };
    }

    componentDidMount() {
        this.getPreviews();
    }

    getPreviews = () => {
        const { dataModel, version } = this.props;

        this.setState({
            isLoadingPreviews: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/rdf')
            .then((response) => {
                this.setState({
                    previews:          response.data,
                    isLoadingPreviews: false,
                    hasLoadedPreviews: true,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingPreviews: false,
                });
            });
    };

    render() {
        const { hasLoadedPreviews, previews } = this.state;

        if (!hasLoadedPreviews) {
            return <InlineLoader />;
        }

        const tabs = previews.modules.map((element) => {
            return {
                title:   `Module ${element.order}. ${element.title}`,
                badge:   element.repeated && 'Repeated',
                content: <DataModelModulePreview rdf={element.rdf} />
            }
        });

        return <div className="PageBody">
            {previews.modules.length === 0 ? <div className="NoResults">This data model does not have modules.</div> : <SideTabs
                hasTabs
                tabs={[
                    {
                        title: 'Full data model',
                        content: <DataModelModulePreview rdf={previews.full} />
                    },
                    {
                        type: 'separator'
                    },
                    ...tabs
                ]}
            />}
        </div>;
    }
}