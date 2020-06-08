import React, {Component} from "react";
import axios from "axios/index";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import NotFound from "../../NotFound";
import {Route, Switch} from "react-router-dom";
import Nav from "react-bootstrap/Nav";
import {LinkContainer} from "react-router-bootstrap";
import Icon from "../../../components/Icon";
import DataModelPrefixes from "./DataModelPrefixes";
import DataModelModules from "./DataModelModules";
import DataModelDetails from "./DataModelDetails";
import DataModelNodes from "./DataModelNodes";
import {Button} from "@castoredc/matter";
import DataModelPreview from "./DataModelPreview";

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
            isLoadingDataModels: true,
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
                    isLoadingDataModels: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the data models';
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

    render() {
        const { match } = this.props;
        const { dataModel, isLoadingDataModel, showModal } = this.state;

        if (isLoadingDataModel) {
            return <InlineLoader/>;
        }

        return <div className="PageContainer">
            <Row className="PageHeader">
                <Col sm={2} className="Back">
                    <LinkContainer to={'/admin/model'}>
                        <Button buttonType="secondary" icon="arrowLeftChevron">
                            Back to data models
                        </Button>
                    </LinkContainer>
                </Col>
                <Col sm={10} className="PageTitle">
                    <div><h3>{dataModel.title}</h3></div>
                </Col>
            </Row>
            <Row>
                <Col sm={2} className="LeftNav">
                    <Nav className="flex-column">
                        <LinkContainer to={'/admin/model/' + dataModel.id} exact={true}>
                            <Nav.Link>Data model</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/modules'} exact={true}>
                            <Nav.Link>Modules</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/nodes'} exact={true}>
                            <Nav.Link>Nodes</Nav.Link>
                        </LinkContainer>
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/prefixes'} exact={true}>
                            <Nav.Link>Prefixes</Nav.Link>
                        </LinkContainer>
                        <hr />
                        <LinkContainer to={'/admin/model/' + dataModel.id + '/preview'} exact={true}>
                            <Nav.Link>Preview</Nav.Link>
                        </LinkContainer>
                    </Nav>
                </Col>
                <Col sm={10} className="Page">
                    <Switch>
                        <Route path="/admin/model/:model" exact
                               render={(props) => <DataModelDetails {...props} dataModel={dataModel} />} />
                        <Route path="/admin/model/:model/modules" exact
                               render={(props) => <DataModelModules {...props} dataModel={dataModel} />} />
                        <Route path="/admin/model/:model/nodes" exact
                               render={(props) => <DataModelNodes {...props} dataModel={dataModel} />} />
                        <Route path="/admin/model/:model/prefixes" exact
                               render={(props) => <DataModelPrefixes {...props} dataModel={dataModel} />} />
                        <Route path="/admin/model/:model/preview" exact
                               render={(props) => <DataModelPreview {...props} dataModel={dataModel} />} />
                        <Route component={NotFound} />
                    </Switch>
                </Col>
            </Row>
        </div>;
    }
}