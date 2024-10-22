import { PersonType } from './PersonType';
import { ValueLabel } from 'types/Types';

export type UserType = {
    id: string;
    details: PersonType | null;
    isAdmin: boolean;
    linkedAccounts: LinkedAccountsType;
    wizards: WizardsType;
    suggestions: any;
};

export type WizardsType = {
    [key: string]: boolean,
}

export type LinkedAccountsType = {
    castor: CastorAccountType | null;
    orcid: OrcidAccountType | null;
};

export type CastorAccountType = {
    id: string;
    nameFirst: string;
    nameMiddle: string | null;
    nameLast: string;
    emailAddress: string;
};

export type OrcidAccountType = {
    orcid: string;
    name: string;
};
