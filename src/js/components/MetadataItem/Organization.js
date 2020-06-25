import React, {Component} from 'react'

import './MetadataItem.scss'
import {classNames} from "../../util";
import {Icon} from "@castoredc/matter";

export default class Organization extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showDepartment: false,
        };
    }

    toggleDepartment = () => {
        const { showDepartment } = this.state;
        const { department } = this.props;

        if(!department) {
            return false;
        }

        this.setState({
            showDepartment: !showDepartment,
        });
    };

    render() {
        const { name, department, country, city } = this.props;
        const { showDepartment } = this.state;

        return <div className="Organization">
            <div className={classNames('Center', department && 'HasDepartment')} onClick={this.toggleDepartment}>
                {name}

                {department && <div className={classNames('ToggleArrow', showDepartment && 'Active')}>
                    <Icon type="arrowBottom" width="12px" height="12px" />
                </div>}
            </div>
            {department && <div className={classNames('Department', showDepartment && 'Show')}>
                {department}
            </div>}
            <div className="Location">
                {city}, {country}
            </div>
        </div>;
    }
}