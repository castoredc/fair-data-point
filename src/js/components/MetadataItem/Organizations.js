import React, {Component} from 'react'

import './MetadataItem.scss'
import MetadataItem from "./index";

const Organization = ({name, url, type, email, center}) => {
    if(email)
    {
        url = 'mailto:' + email;
    }

    if(type === "department")
    {
        return <div className="Organization Department">
            <div className="Center">
                {center.name}
            </div>
            <div className="Department">
                {name}
            </div>
            <div className="Location">
                {center.city}, {center.country}
            </div>
        </div>;
    }

    return <div className="Organization">
        <div className="Center">
            {name}
        </div>
        <div className="Location">
            {center.city}, {center.country}
        </div>
    </div>;
};

class Organizations extends Component {
    render() {
        const { organizations } = this.props;

        const label = 'Organization' + (organizations.length > 1 ? 's' : '');

        return <MetadataItem label={label} className="Organizations">
            {organizations.map((organization, index) => {
                return <Organization key={index} name={organization.name} url={organization.url} type={organization.type} center={organization.center} />
            })}
        </MetadataItem>
    }
}

export default Organizations