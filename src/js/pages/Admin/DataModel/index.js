import React, {Component} from "react";
import axios from "axios";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import NotFound from "../../NotFound";
import {Route, Switch, Redirect} from "react-router-dom";
import Nav from "react-bootstrap/Nav";
import {LinkContainer} from "react-router-bootstrap";
import DataModelPrefixes from "./DataModelPrefixes";
import DataModelModules from "./DataModelModules";
import DataModelDetails from "./DataModelDetails";
import DataModelNodes from "./DataModelNodes";
import {Button, Dropdown as CastorDropdown} from "@castoredc/matter";
import DataModelPreview from "./DataModelPreview";
import Dropdown from "../../../components/Input/Dropdown";

export default class DataModel extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDataModel:  true,
            hasLoadedDataModel:  false,
            dataModel:           null,
            showModal:           false
        };
    }

    componentDidMount() {
        this.getDataModel();
    }

    getDataModel = () => {
        const { match } = this.props;

        this.setState({
            isLoadingDataModel: true,
        });

        axios.get('/api/model/' + match.params.model)
            .then((response) => {
                this.setState({
                    dataModel:          response.data,
                    isLoadingDataModel: false,
                    hasLoadedDataModel: true,
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
        console.log(version);
    };

    render() {
        const { match } = this.props;
        const { dataModel, isLoadingDataModel, showModal } = this.state;

        if (isLoadingDataModel) {
            return <InlineLoader/>;
        }

        const versions = dataModel.versions.map((version) => {
            return {value: version.id, label: version.version};
        });

        const currentVersionNumber = match.params.version ? match.params.version : versions.slice(-1)[0].label;
        const currentVersion = versions.find(({label}) => label === currentVersionNumber);

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    <LinkContainer to={'/admin/models'}>
                        <Button buttonType="secondary" icon="arrowLeftChevron">
                            Back to data models
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={8} className="PageTitle">
                    <div><h3>{dataModel.title}</h3></div>
                </Col>
                <Col sm={2} className="Version">
                    <CastorDropdown
                        onChange={(e) => {this.handleVersionChange(e.value)}}
                        value={currentVersion}
                        options={versions}
                        menuPlacement="auto"
                        width="fullWidth"
                    />
                </Col>
            </Row>
            <Row className="FillHeight">
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersionNumber} exact={true}>
                            <Nav.Link>Data model</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersionNumber + '/modules'} exact={true}>
                            <Nav.Link>Modules</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersionNumber + '/nodes'} exact={true}>
                            <Nav.Link>Nodes</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersionNumber + '/prefixes'} exact={true}>
                            <Nav.Link>Prefixes</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/' + currentVersionNumber + '/preview'} exact={true}>
                            <Nav.Link>Preview</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Redirect exact from={'/admin/model/' + dataModel.id} to={'/admin/model/' + dataModel.id + '/' + currentVersionNumber} />

                        <Route path="/admin/model/:model/:version" exact
                               render={(props) => <DataModelDetails {...props} dataModel={dataModel} version={currentVersion.value} />} />
                        <Route path="/admin/model/:model/:version" exact
                               render={(props) => <DataModelDetails {...props} dataModel={dataModel} version={currentVersion.value} />} />
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