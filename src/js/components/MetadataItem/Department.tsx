import React from 'react';
import { DepartmentType } from 'types/DepartmentType';

type DepartmentProps = DepartmentType;

const Department: React.FC<DepartmentProps> = ({ name }) => {
    return <span className="Department">{name}</span>;
};

export default Department;