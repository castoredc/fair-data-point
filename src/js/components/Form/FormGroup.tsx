import React, {FC} from 'react';

type FormGroupProps = {
    label: string
}

const FormGroup: FC<FormGroupProps> = ({ label, children }) => {
    return <div className="FormGroup">
        <div className="FormHeading">
            <h2>{label}</h2>
        </div>
        <div className="FormBody">
            {children}
        </div>
    </div>;
}

export default FormGroup;
