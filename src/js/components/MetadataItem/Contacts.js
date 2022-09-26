import React, { Component } from 'react';

import './MetadataItem.scss';
import { Button } from '@castoredc/matter';
import Person from './Person';

class Contacts extends Component {
    render() {
        const { contacts } = this.props;
        let emails = [];

        return (
            <div className="Contacts">
                <span>By </span>

                {contacts.map((contact, index) => {
                    if (contact.type === 'person' && contact.person.email) {
                        emails.push(contact.person.email);
                    }

                    return (
                        <span className="Contact" key={index}>
                            {contact.type === 'person' && <Person person={contact.person} />}
                            {index !== contacts.length - 1 && ', '}
                        </span>
                    );
                })}

                {emails.length > 0 && (
                    <Button buttonType="bare" icon="email" href={`mailto:${emails[0]}`}>
                        Get in touch
                    </Button>
                )}
            </div>
        );
    }
}

export default Contacts;
