import React from 'react';
import CSVStudyStructure from 'components/StudyStructure/CSVStudyStructure';

interface DistributionContentsCsvProps {
    contents: any[];
    catalog: string;
    distribution: any;
    dataset: string;
}

const DistributionContentsCsv: React.FC<DistributionContentsCsvProps> = ({
                                                                             contents,
                                                                             catalog,
                                                                             distribution,
                                                                             dataset,
                                                                         }) => {
    return (
        <div className="PageContainer">
            {distribution.includeAllData ? (
                <div className="NoResults">This distribution contains all fields.</div>
            ) : (
                <CSVStudyStructure
                    studyId={distribution.study.id}
                    distributionContents={contents}
                    catalog={catalog}
                    dataset={dataset}
                    distribution={distribution.slug}
                />
            )}
        </div>
    );
};

export default DistributionContentsCsv;
