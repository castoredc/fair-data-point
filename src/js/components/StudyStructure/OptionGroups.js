import React, {Component} from 'react'
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import Col from "react-bootstrap/Col";
import InlineLoader from "../LoadingScreen/InlineLoader";
import OptionGroup from "./OptionGroup";
import './StudyStructure.scss';

export default class OptionGroups extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingOptionGroups:   true,
            hasLoadedOptionGroups:   false,
            optionGroups:            null,
        };
    }

    componentDidMount() {
        this.getOptionGroups();
    }

    componentDidUpdate(prevProps) {
        if (this.props.shouldUpdate === true && this.props.shouldUpdate !== prevProps.shouldUpdate) {
            this.getOptionGroups();
        }
    }

    getOptionGroups = () => {
        const { studyId, onUpdate } = this.props;

        this.setState({
            isLoadingOptionGroups: true,
        });

        axios.get('/api/study/' + studyId + '/optiongroups')
            .then((response) => {
                this.setState({
                    optionGroups:          response.data,
                    isLoadingOptionGroups: false,
                    hasLoadedOptionGroups: true,
                });

                onUpdate();
            })
            .catch((error) => {
                if (error.response && typeof error.response.data.error !== "undefined") {
                    toast.error(<ToastContent type="error" message={error.response.data.error}/>);
                } else {
                    toast.error(<ToastContent type="error" message="An error occurred"/>);
                }

                this.setState({
                    isLoadingOptionGroups: false,
                });
            });
    };


    render() {
        const { hasLoadedOptionGroups, optionGroups } = this.state;
        const { openModal } = this.props;

        if (!hasLoadedOptionGroups) {
            return <InlineLoader />;
        }

        return optionGroups.length === 0 ? <Col sm={12} className="NoResults">This study does not have option groups.</Col> : <div className="OptionGroups">
                {optionGroups.map((optionGroup) => {
                    return <OptionGroup key={optionGroup.id} id={optionGroup.id} name={optionGroup.name} options={optionGroup.options} openModal={openModal} />
                })}
            </div>
    }

}