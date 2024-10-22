import React, { FunctionComponent } from 'react';
import BackButton, { BackButtonProps } from 'components/BackButton';
import DocumentTitle from 'components/DocumentTitle';
import { Heading, Separator } from '@castoredc/matter';
import './SelectPage.scss';

interface SelectPageProps {
    title: string;
    description?: string;
    children: React.ReactNode;
    backButton: BackButtonProps & { label: string };
    returnButton?: boolean;
    sidebar?: boolean;
}
export const SelectPage: FunctionComponent<SelectPageProps> = ({ title, description, children, backButton }) => {
    return (
        <div className="SelectPage">
            <DocumentTitle title={title} />
            <div className="SelectPageHeader">
                <BackButton to={backButton.to}>{backButton.label}</BackButton>

                <Heading type="Section">{title}</Heading>

                {description && <p>{description}</p>}

                <Separator />
            </div>

            <div className="SelectPageContent">{children}</div>
        </div>
    );
};

export default SelectPage;
