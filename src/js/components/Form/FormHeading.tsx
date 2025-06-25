import React, { FC } from 'react';
import { Heading } from '@castoredc/matter';

interface FormHeadingProps {
    label: string;
}

const FormHeading: FC<FormHeadingProps> = ({ label }) => {
    return (
        <div className="FormHeading">
            <Heading type="Subsection">{label}</Heading>
        </div>
    );
};

export default FormHeading;
