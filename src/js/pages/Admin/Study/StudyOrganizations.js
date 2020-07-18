import React, {Component} from "react";
import OrganizationsForm from "../../../components/Form/Study/OrganizationsForm";

export default class StudyOrganizations extends Component {
    render() {
        const { study } = this.props;

        return <div className="PageBody">
            <OrganizationsForm
                studyId={study.id}
                admin={true}
            />
        </div>;
    }
}