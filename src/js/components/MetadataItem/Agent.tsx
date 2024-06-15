import React from 'react';

import './MetadataItem.scss';
import { GenericAgentType } from 'types/AgentListType';

type AgentProps = GenericAgentType;

const Agent: React.FC<AgentProps> = ({type, id, name}) => {
    return <div className="Agent">
        {name}
    </div>;
}

export default Agent;
