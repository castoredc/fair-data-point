import React, {Component} from "react";
import axios from "axios";
import Header from "../../../components/Layout/Header";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Layout from "../../../components/Layout";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import AssociatedItemsBar from "../../../components/AssociatedItemsBar";
import DatasetList from "../../../components/List/DatasetList";
import CatalogList from "../../../components/List/CatalogList";
import DistributionList from "../../../components/List/DistributionList";

export default class Agent extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            agent: null,
            currentItem: null,
        };
    }

    componentDidMount() {
        this.getAgent();
    }

    getAgent = () => {
        const {match} = this.props;

        axios.get('/api/agent/details/' + match.params.slug)
            .then((response) => {
                this.setState({
                    agent: response.data,
                    currentItem: Object.keys(response.data.count).find(key => response.data.count[key] > 0) ?? null,
                    isLoading: false,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the details';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handleItemChange = (item) => {
        this.setState({
            currentItem: item
        });
    }

    render() {
        const {isLoading, agent, currentItem} = this.state;
        const {user, embedded, location} = this.props;

        const title = agent ? agent.name : null;

        const breadcrumbs = getBreadCrumbs(location, {agent});

        return <Layout
            className="Agent"
            title={title}
            isLoading={isLoading}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} title={title}/>

            <MainBody isLoading={isLoading}>
                {agent && (<>
                    <AssociatedItemsBar items={agent.count} current={currentItem} onClick={this.handleItemChange}/>

                    <CatalogList
                        visible={currentItem === 'catalog'}
                        agent={agent}
                        state={breadcrumbs.current ? breadcrumbs.current.state : null}
                        embedded={embedded}
                        className="MainCol"
                    />

                    <DatasetList
                        visible={currentItem === 'dataset'}
                        agent={agent}
                        state={breadcrumbs.current ? breadcrumbs.current.state : null}
                        embedded={embedded}
                        className="MainCol"
                    />

                    <DistributionList
                        visible={currentItem === 'distribution'}
                        agent={agent}
                        state={breadcrumbs.current ? breadcrumbs.current.state : null}
                        embedded={embedded}
                        className="MainCol"
                    />
                </>)}
            </MainBody>
        </Layout>;
    }
}
