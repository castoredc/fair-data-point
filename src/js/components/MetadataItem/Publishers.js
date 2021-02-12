import React, {Component} from 'react'

import './MetadataItem.scss'
import CustomIcon from "../Icon/CustomIcon";
import Organization from "./Organization";
import Person from "./Person";

class Publishers extends Component {
    render() {
        const {publishers} = this.props;

        return <div className="Publishers">
            {publishers.map((publisher, index) => {
                let component = null;

                if (publisher.type === 'person') {
                    component = <Person person={publisher.person} />;
                } else if (publisher.type === 'organization') {
                    component = <Organization
                        organization={publisher.organization}
                        department={publisher.hasDepartment && publisher.department}
                        small
                    />;
                }

                return <div className="Publisher" key={index}>
                    {component}
                </div>
            })}
        </div>
    }
}

export default Publishers