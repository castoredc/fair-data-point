import React, {Component} from "react";
import StudyDetailsForm from "../../../components/Form/StudyDetailsForm";

export default class StudyMetadata extends Component {
    render() {
        const { study, onSave } = this.props;

        return <div className="PageBody">
            <StudyDetailsForm
                studyId={study.id}
                admin={true}
                onSave={onSave}
            />
        </div>;
    }
}
