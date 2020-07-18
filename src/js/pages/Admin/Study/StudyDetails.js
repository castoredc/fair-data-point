import React, {Component} from "react";
import StudyForm from "../../../components/Form/Admin/StudyForm";

export default class StudyDetails extends Component {
    render() {
        const { study, onSave } = this.props;

        return <div className="PageBody">
            <StudyForm
                study={study}
            />
        </div>;
    }
}
