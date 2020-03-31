import React, {Component} from "react";
import axios from "axios/index";

import ListItem from "../../../components/ListItem";
import Button from "react-bootstrap/Button";
import {Redirect} from "react-router-dom";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import LoadingScreen from "../../../components/LoadingScreen";
import CatalogSteppedForm from "../../../components/Form/CatalogSteppedForm";
import {localizedText} from "../../../util";

export default class AddStudy extends Component {
    constructor(props) {
        super(props);

        this.state = {
            selectedStudy: null,
            studies: {},
            isLoading: true,
            isSaved: false,
            submitDisabled: true
        };
    }

    getStudies = () => {
        axios.get('/api/castor/studies')
            .then((response) => {
                this.setState({
                    studies: response.data,
                    isLoading: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoading: false,
                });

                if(error.response && typeof error.response.data.error !== "undefined")
                {
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred" />);
                }
            });
    };

    componentDidMount() {
        this.getStudies();
    }

    handleStudySelect = (studyId) => {
        this.setState({
            selectedStudy: studyId,
            submitDisabled: false
        })
    };

    handleNext = () => {
        this.setState({
            submitDisabled: true
        });

        axios.post('/api/catalog/' + this.props.match.params.catalog + '/study/add', {
            studyId: this.state.selectedStudy
        })
        .then((response) => {
            this.setState({
                isSaved: true
            });
        })
            .catch((error) => {
                if(error.response && error.response.status === 409)
                {
                    this.setState({
                        isSaved: true
                    });
                }
                else if(error.response && typeof error.response.data.error !== "undefined")
                {
                    this.setState({
                        submitDisabled: false
                    });
                    toast.error(<ToastContent type="error" message={error.response.data.error} />);
                }
            })
    };

    render() {
        if(this.state.isLoading)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        if(this.state.isSaved)
        {
            return <Redirect push to={'/my-studies/' + this.props.match.params.catalog + '/study/' + this.state.selectedStudy + '/metadata/details'} />;
        }

        return <CatalogSteppedForm
            catalog={this.props.catalog}
            currentStep={1}
            smallHeading="Step One"
            heading="Choose a Study"
            description={'Please choose an item from your list of studies that you’d like to include in the ' + localizedText(this.props.catalog.title, 'en') + '.'}
        >
            {this.state.studies.length > 0 ? this.state.studies.map((study) => {
                    return <ListItem key={study.id}
                                     title={study.name}
                                     selectable={true}
                                     active={this.state.selectedStudy === study.id}
                                     onClick={() => {this.handleStudySelect(study.id)}}
                                     leftIcon="study"
                    />
                }
            ) : <div className="NoResults">No studies found.</div>}

            <Row className="FullScreenSteppedFormButtons">
                <Col>&nbsp;</Col>
                <Col>
                    <Button disabled={this.state.submitDisabled} onClick={this.handleNext}>Next</Button>
                </Col>
            </Row>
        </CatalogSteppedForm>;
    }
}
