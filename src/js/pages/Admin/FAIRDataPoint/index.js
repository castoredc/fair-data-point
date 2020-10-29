import React, {Component} from "react";
import axios from "axios";
import {localizedText} from "../../../util";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import {ViewHeader} from "@castoredc/matter";
import FairDataPointMetadata from "./FairDataPointMetadata";
import DocumentTitle from "../../../components/DocumentTitle";
import SideBar from "../../../components/SideBar";

export default class FAIRDataPoint extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFairDataPoint: true,
            fdp:          null,
        };
    }

    componentDidMount() {
        this.getFairDataPoint();
    }

    getFairDataPoint = () => {
        this.setState({
            isLoadingFairDataPoint: true,
        });

        axios.get('/api/fdp')
            .then((response) => {
                this.setState({
                    fdp:          response.data,
                    isLoadingFairDataPoint: false,
                });
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    this.setState({
                        isLoadingFairDataPoint: false,
                        hasError:         true,
                        errorMessage:     error.response.data.error,
                    });
                } else {
                    this.setState({
                        isLoadingFairDataPoint: false,
                    });
                }
            });
    };

    render() {
        const {fdp, isLoadingFairDataPoint} = this.state;
        const {location} = this.props;

        if (isLoadingFairDataPoint) {
            return <InlineLoader/>;
        }

        const title = fdp.hasMetadata ? localizedText(fdp.metadata.title, 'en') : null;

        return <div className="PageContainer">
            <DocumentTitle title={'FDP Admin | FDP'} />

            <SideBar
                location={location}
                items={[
                    {
                        to: '/admin/fdp/metadata',
                        exact: true,
                        title: 'Metadata',
                        customIcon: 'metadata'
                    },
                ]}
            />

            <div className="Page">
                <div className="PageTitle">
                    {title && <ViewHeader>{title}</ViewHeader>}
                </div>

                <Switch>
                    <Route path="/admin/fdp/metadata" exact
                           render={(props) => <FairDataPointMetadata {...props} fdp={fdp}
                                                               onSave={this.getFairDataPoint}/>}/>
                    <Route component={NotFound}/>
                </Switch>
            </div>
        </div>;
    }
}