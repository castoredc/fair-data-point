import React from 'react';
import CSVStudyStructure from 'components/StudyStructure/CSVStudyStructure';
import NoResults from 'components/NoResults';

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
                <NoResults>This distribution contains all fields.</NoResults>
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
