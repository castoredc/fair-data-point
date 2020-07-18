import React, {Component} from "react";
import OptionGroups from "../../../components/StudyStructure/OptionGroups";
import AddAnnotationModal from "../../../modals/AddAnnotationModal";
import {Tabs} from "@castoredc/matter";

export default class StudyAnnotations extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal:    false,
            modalData:    null,
            shouldUpdate: true,
            selectedType: 'optionGroups',
        };
    }

    openModal = (entity) => {
        this.setState({
            showModal: true,
            modalData: entity,
        });
    };

    closeModal = () => {
        this.setState({
            showModal: false,
        });
    };

    onSaved = () => {
        this.closeModal();

        this.setState({
            shouldUpdate: true,
        });
    };

    onUpdate = () => {
        this.setState({
            shouldUpdate: false,
        });
    };

    changeTab = (tabIndex) => {
        this.setState({
            selectedType: tabIndex,
        });
    };

    render() {
        const {study} = this.props;
        const {showModal, modalData, shouldUpdate, selectedType} = this.state;

        return <div className="PageBody">
            <AddAnnotationModal
                show={showModal}
                entity={modalData}
                handleClose={this.closeModal}
                onSaved={this.onSaved}
                studyId={study.id}
            />

            <div className="PageTabs">
                <Tabs
                    onChange={this.changeTab}
                    selected={selectedType}
                    tabs={{
                        optionGroups: {
                            title:   'Option Groups',
                            content: <OptionGroups studyId={study.id} openModal={this.openModal} shouldUpdate={shouldUpdate}
                                                   onUpdate={this.onUpdate}/>,
                        },
                    }}
                />
            </div>
        </div>;
    }
}