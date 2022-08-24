import { OrganizationType } from './OrganizationType';
import { DepartmentType } from './DepartmentType';

export type AffiliationType = {
    organization: OrganizationType;
    department: DepartmentType;
    position: string;
    country: string;
};
