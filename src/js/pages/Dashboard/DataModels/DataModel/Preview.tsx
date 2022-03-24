import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import {LoadingOverlay} from "@castoredc/matter";
import ToastContent from "components/ToastContent";
import DataModelModulePreview from "components/DataModelModule/DataModelModulePreview";
import SideTabs from "components/SideTabs";
import {AuthorizedRouteComponentProps} from "components/Route";
import PageBody from "components/Layout/Dashboard/PageBody";

interface PreviewProps extends AuthorizedRouteComponentProps {
    dataModel: any,
    version: any,
}

interface PreviewState {
    isLoading: boolean,
    previews: any,
}

export default class Preview extends Component<PreviewProps, PreviewState> {
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
        const {dataModel, version} = this.props;

        this.setState({
            isLoading: true,
        });

        axios.get('/api/model/' + dataModel.id + '/v/' + version + '/rdf')
            .then((response) => {
                this.setState({
                    previews: response.data,
                    isLoading: false,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoading: false,
                });
            });
    };

    render() {
        const {isLoading, previews} = this.state;

        if (isLoading) {
            return <LoadingOverlay accessibleLabel="Loading preview"/>;
        }

        if (previews.modules.length === 0) {
            return <div className="NoResults">This data model does not have groups.</div>;
        }

        const tabs = previews.modules.map((element) => {
            let icons = [] as any;

            if (element.repeated) {
                icons.push({
                    icon: 'copy',
                    title: 'This group is repeated'
                });
            }

            if (element.dependent) {
                icons.push({
                    icon: 'decision',
                    title: 'This group is dependent'
                });
            }

            return {
                number: element.order,
                title: element.title,
                icons: icons,
                content: <DataModelModulePreview repeated={element.repeated} dependent={element.dependent}
                                                 dependencies={element.dependencies} rdf={element.rdf}
                                                 visualization={element.visualization}/>
            }
        });

        return <PageBody>
            <SideTabs
                hasTabs
                title="Groups"
                tabs={[
                    {
                        title: 'Full data model',
                        content: <DataModelModulePreview rdf={previews.full} visualization={previews.visualization}/>
                    },
                    {
                        type: 'separator'
                    },
                    ...tabs
                ]}
            />
        </PageBody>;
    }
}