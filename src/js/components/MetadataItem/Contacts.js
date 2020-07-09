import React, {Component} from 'react'

import './MetadataItem.scss'
import {Button} from "@castoredc/matter";

const Contact = ({name, url, type, email}) => {
    return <span className="Contact">
        {name}
    </span>;
};

class Contacts extends Component {
    render() {
        const {contacts} = this.props;
        let emails = [];

        return <div className="Contacts">
            <span>By </span>

            {contacts.map((contact, index) => {
                if (contact.email) {
                    emails.push(contact.email);
                }

                return <span key={index}>
                    <Contact name={contact.name} url={contact.url} type={contact.type} email={contact.email}/>
                    {index !== (contacts.length - 1) && ', '}
                </span>
            })}

            {emails.length > 0 && <Button buttonType="contentOnly" icon="email" href={`mailto:${emails[0]}`}>Get in touch</Button>}
        </div>
    }
}

export default Contacts