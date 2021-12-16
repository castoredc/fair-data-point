import React, {Component} from "react";
import CSVStudyStructure from "components/StudyStructure/CSVStudyStructure";

export default class DistributionContentsCsv extends Component {
    render() {
        const {contents, catalog, distribution, dataset} = this.props;

        return <div className="PageContainer">

            {distribution.includeAllData ? <div className="NoResults">
                This distribution contains all fields.
            </div> : <CSVStudyStructure
                studyId={distribution.study.id}
                distributionContents={contents}
                catalog={catalog}
                dataset={dataset}
                distribution={distribution.slug}
            />}
        </div>;
    }
}