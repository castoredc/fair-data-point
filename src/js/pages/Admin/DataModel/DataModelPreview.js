import React, {Component} from 'react'
import {Col, Row} from "react-bootstrap";
import axios from "axios";
import {toast} from "react-toastify";
import InlineLoader from "../../../components/LoadingScreen/InlineLoader";
import DataModelModule from "../../../components/DataModelModule/DataModelModule";
import ToastContent from "../../../components/ToastContent";
import DataModelModulePreview from "../../../components/DataModelModule/DataModelModulePreview";
import Toggle from "../../../components/Toggle";
import Highlight from "../../../components/Highlight";

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
        const { dataModel } = this.props;

        this.setState({
            isLoadingPreviews: true,
        });

        axios.get('/api/model/' + dataModel.id + '/rdf')
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
        const { dataModel } = this.props;
        const { showModal, hasLoadedPreviews, previews } = this.state;

        if (!hasLoadedPreviews) {
            return <InlineLoader />;
        }

        return <div>
            <Row>
                <Col sm={12}>
                    {previews.modules.length === 0 ? <div className="NoResults">This data model does not have modules.</div> : <div>
                        <Toggle title="Full data model">
                            <Highlight content={previews.full} />
                        </Toggle>

                        {previews.modules.map((element) => {
                            return <DataModelModulePreview
                                key={element.id}
                                id={element.id}
                                title={element.title}
                                order={element.order}
                                rdf={element.rdf}
                            />;
                        })}
                    </div>}
                </Col>
            </Row>
        </div>;
    }
}