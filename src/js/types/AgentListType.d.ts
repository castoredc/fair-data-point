import { OrganizationType } from 'types/OrganizationType';
import { DepartmentType } from 'types/DepartmentType';
import { PersonType } from 'types/PersonType';

export type AgentListType = AgentListItemType[];

export type AgentListItemType = AgentListOrganizationType | AgentListDepartmentType | AgentListPersonType;

export type GenericAgentType = {
    id: string;
    type: string;
    name: string;
};

export interface AgentListOrganizationType extends GenericAgentType {
    type: 'organization';
    hasDepartment: false;
    organization: OrganizationType;
}

export interface AgentListDepartmentType extends GenericAgentType {
    type: 'organization';
    hasDepartment: true;
    department: DepartmentType;
    organization: OrganizationType;
}

export interface AgentListPersonType extends GenericAgentType {
    type: 'person';
    person: PersonType;
}
