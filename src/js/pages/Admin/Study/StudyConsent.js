import React, {Component} from "react";
import ConsentForm from "../../../components/Form/ConsentForm";

export default class StudyConsent extends Component {
    render() {
        const { study } = this.props;

        return <div className="PageBody">
            <ConsentForm
                studyId={study.id}
                admin={true}
            />
        </div>;
    }
}