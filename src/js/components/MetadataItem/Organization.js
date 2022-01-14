import React, {Component} from 'react'

import './MetadataItem.scss'
import {classNames} from "../../util";
import Department from "./Department";

export default class Organization extends Component {
    render() {
        const {organization, department, small} = this.props;

        const {name, country, city} = organization;

        if (small) {
            return <div className="Organization">
                <div className={classNames('Center')}>
                    {name}

                    {department && <>,&nbsp;
                        <Department small={small} {...department} />
                    </>}
                </div>
            </div>;
        }

        return <div className="Organization">
            <div className={classNames('Center', department && 'HasDepartment')} onClick={this.toggleDepartment}>
                {name}

                {department && <>,&nbsp;
                    <Department small={small} {...department} />
                </>}
            </div>
            <div className="Location">
                {city}, {country}
            </div>
        </div>;
    }
}