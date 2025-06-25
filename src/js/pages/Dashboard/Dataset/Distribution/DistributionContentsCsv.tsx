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

    if (distribution.includeAllData) {
        return <NoResults>This distribution contains all fields.</NoResults>;
    }

    return <CSVStudyStructure
        studyId={distribution.study.id}
        distributionContents={contents}
        catalog={catalog}
        dataset={dataset}
        distribution={distribution.slug}
    />;
};

export default DistributionContentsCsv;
