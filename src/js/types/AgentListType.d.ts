import { OrganizationType } from 'types/OrganizationType';
import { DepartmentType } from 'types/DepartmentType';
import { PersonType } from 'types/PersonType';

export type AgentListType = AgentListItemType[];

export type AgentListItemType = AgentListOrganizationType | AgentListDepartmentType | AgentListPersonType;

export type AgentListOrganizationType = {
    type: 'organization';
    hasDepartment: false;
    organization: OrganizationType;
};

export type AgentListDepartmentType = {
    type: 'organization';
    hasDepartment: true;
    department: DepartmentType;
    organization: OrganizationType;
};

export type AgentListPersonType = {
    type: 'person';
    person: PersonType;
};
