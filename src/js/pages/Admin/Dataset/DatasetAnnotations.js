import React, {Component} from "react";
import Tab from "react-bootstrap/Tab";
import Nav from "react-bootstrap/Nav";
import OptionGroups from "../../../components/StudyStructure/OptionGroups";
import AddAnnotationModal from "../../../modals/AddAnnotationModal";

export default class DatasetAnnotations extends Component {
    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            modalData: null,
            shouldUpdate: true
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
            showModal: false
        });
    };

    onSaved = () => {
        this.closeModal();

        this.setState({
            shouldUpdate: true
        });
    };

    onUpdate = () => {
        this.setState({
            shouldUpdate: false
        });
    };

    render() {
        const { studyId } = this.props;
        const { showModal, modalData, shouldUpdate } = this.state;

        return <div>
            <AddAnnotationModal
                show={showModal}
                entity={modalData}
                handleClose={this.closeModal}
                onSaved={this.onSaved}
                studyId={studyId}
            />
            <Tab.Container id="DatasetAnnotations" defaultActiveKey="optionGroup">
                <div className="TabTabs DatasetAnnotationsTabs">
                    <Nav variant="tabs">
                        <Nav.Item>
                            <Nav.Link eventKey="optionGroup">Option Groups</Nav.Link>
                        </Nav.Item>
                    </Nav>
                </div>
                <Tab.Content>
                    <Tab.Pane eventKey="optionGroup">
                        <OptionGroups studyId={studyId} openModal={this.openModal} shouldUpdate={shouldUpdate} onUpdate={this.onUpdate} />
                    </Tab.Pane>
                </Tab.Content>
            </Tab.Container>

        </div>;
    }
}