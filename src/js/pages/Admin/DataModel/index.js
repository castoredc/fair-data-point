import React, {Component} from "react";
import axios from "axios";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import NotFound from "../../NotFound";
import {Route, Switch, Redirect, generatePath} from "react-router-dom";
import Nav from "react-bootstrap/Nav";
import {LinkContainer} from "react-router-bootstrap";
import DataModelPrefixes from "./DataModelPrefixes";
import DataModelModules from "./DataModelModules";
import DataModelDetails from "./DataModelDetails";
import DataModelNodes from "./DataModelNodes";
import {Button, Dropdown as CastorDropdown} from "@castoredc/matter";
import DataModelPreview from "./DataModelPreview";
import FormItem from "../../../components/Form/FormItem";
import DataModelVersions from "./DataModelVersions";

export default class DataModel extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingDataModel:  true,
            hasLoadedDataModel:  false,
            dataModel:           null,
            showModal:           false,
            currentVersion:      null
        };
    }

    componentDidMount() {
        this.getDataModel();
    }

    componentDidUpdate(prevProps) {
        const { match } = this.props;

        if (match.params.version !== prevProps.match.params.version) {
            this.getDataModel();
        }
    }

    getDataModel = () => {
        const { match } = this.props;

        this.setState({
            isLoadingDataModel: true,
        });

        axios.get('/api/model/' + match.params.model)
            .then((response) => {
                const versions = response.data.versions.map((version) => {
                    return {value: version.id, label: version.version};
                });

                const currentVersion = (match.params.version && match.params.version !== 'versions') ? match.params.version : versions.slice(-1)[0].label;

                this.setState({
                    dataModel:          response.data,
                    isLoadingDataModel: false,
                    hasLoadedDataModel: true,
                    versions:           versions,
                    currentVersion:     versions.find(({label}) => label === currentVersion)
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDataModel: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the data model';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    openModal = () => {
        this.setState({
            showModal: true
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false
        });
    };

    handleVersionChange = (version) => {
        const { currentVersion, versions } = this.state;

        const newUrl = window.location.pathname.replace('/' + currentVersion.label + '/', '/' + version + '/');

        if(window.location.pathname !== newUrl) {
            this.props.history.push(newUrl);
        } else {
            this.setState({
                currentVersion: versions.find(({label}) => label === version)
            });
        }
    };

    render() {
        const { match } = this.props;
        const { dataModel, isLoadingDataModel, versions, currentVersion } = this.state;

        if (isLoadingDataModel) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    <LinkContainer to={'/admin/models'}>
                        <Button buttonType="secondary" icon="arrowLeftChevron">
                            Back to data models
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={10} className="PageTitle">
                    <div><h3>{dataModel.title}</h3></div>
                </Col>
            </Row>
            <Row className="FillHeight">
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/model/' + dataModel.id} exact={true}>
                            <Nav.Link>Data model</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/versions'} exact={true}>
                            <Nav.Link>Versions</Nav.Link>
                        </LinkContainer>

                        <hr />

                        <FormItem label="Version">
                            <CastorDropdown
                                onChange={(e) => {this.handleVersionChange(e.label)}}
                                value={currentVersion}
                                options={versions}
                                menuPlacement="auto"
                                width="fullWidth"
                            />
                        </FormItem>

                        <hr />

                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersion.label + '/modules'} exact={true}>
                            <Nav.Link>Modules</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersion.label + '/nodes'} exact={true}>
                            <Nav.Link>Nodes</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersion.label + '/prefixes'} exact={true}>
                            <Nav.Link>Prefixes</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersion.label + '/preview'} exact={true}>
                            <Nav.Link>Preview</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Route path="/admin/model/:model" exact
                               render={(props) => <DataModelDetails {...props} dataModel={dataModel} version={currentVersion.value} />} />
                        <Route path="/admin/model/:model/versions" exact
                               render={(props) => <DataModelVersions {...props} getDataModel={this.getDataModel} dataModel={dataModel} version={currentVersion.value} />} />
                        <Route path="/admin/model/:model/:version/modules" exact
                               render={(props) => <DataModelModules {...props} dataModel={dataModel} version={currentVersion.value}  />} />
                        <Route path="/admin/model/:model/:version/nodes" exact
                               render={(props) => <DataModelNodes {...props} dataModel={dataModel} version={currentVersion.value} />} />
                        <Route path="/admin/model/:model/:version/prefixes" exact
                               render={(props) => <DataModelPrefixes {...props} dataModel={dataModel} version={currentVersion.value} />} />
                        <Route path="/admin/model/:model/:version/preview" exact
                               render={(props) => <DataModelPreview {...props} dataModel={dataModel} version={currentVersion.value} />} />

                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}