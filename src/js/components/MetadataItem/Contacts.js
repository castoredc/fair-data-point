import React, {Component} from 'react'

import './MetadataItem.scss'

const Contact = ({name, url, type, email}) => {
    if(email)
    {
        url = 'mailto:' + email;
    }
    return <a href={url} className="Contact" target="_blank">
        {name}
    </a>;
};

class Contacts extends Component {
    render() {
        const { contacts } = this.props;

        const label = 'Contact' + (contacts.length > 1 ? 's' : '');

        return <div className="Contacts">
            <span>{label}: </span>

            {contacts.map((contact, index) => {
                return <span key={index}>
                    <Contact name={contact.name} url={contact.url} type={contact.type} email={contact.email} />
                    {index !== (contacts.length - 1) && ', '}
                </span>
            })}
        </div>
    }
}

export default Contacts