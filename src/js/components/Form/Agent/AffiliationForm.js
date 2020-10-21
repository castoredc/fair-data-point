import React, {Component} from 'react';

import '../Form.scss'
import OrganizationForm from "./OrganizationForm";
import DepartmentForm from "./DepartmentForm";
import FormItem from "../FormItem";
import Input from "../../Input";
import {classNames} from "../../../util";
import {Stack} from "@castoredc/matter";

export default class AffiliationForm extends Component {
    render() {
        const {data, validation, countries, handleChange} = this.props;

        const required = "This field is required";

        return (
            <div className="Affiliation">
                <OrganizationForm
                    data={data.organization}
                    countries={countries}
                    validation={validation}
                    handleChange={(event, callback) => handleChange('organization', event, callback)}
                />

                <DepartmentForm
                    data={data.department}
                    organization={data.organization}
                    validation={validation}
                    handleChange={(event, callback) => handleChange('department', event, callback)}
                />

                <div className={classNames('Position', data.organization.source === null && 'WaitingOnInput')}>
                    <Stack>
                        <FormItem label="Position">
                            <Input
                                validators={['required']}
                                errorMessages={[required]}
                                name="position"
                                onChange={(event) => handleChange('position', event)}
                                value={data.position.position}
                                readOnly={data.department.source === null}
                            />
                        </FormItem>
                    </Stack>
                </div>
            </div>
        );
    }
}