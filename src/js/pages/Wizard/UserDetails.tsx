import React from 'react';
import Emoji from 'components/Emoji';
import { AuthorizedRouteComponentProps } from 'components/Route';
import UserDetailsForm from 'components/Form/Agent/UserDetailsForm';

interface UserDetailsProps extends AuthorizedRouteComponentProps {
}

const UserDetails: React.FC<UserDetailsProps> = ({ user, history }) => {
    if (user === null) {
        return null;
    }

    return (
        <>
            <header>
                <h1>
                    <Emoji symbol="👋" />
                    &nbsp; Hi {user.details ? user.details.firstName : ''}!
                </h1>
                <div className="Description">Before you continue, please check your details below.</div>
            </header>

            <UserDetailsForm user={user} history={history} />
        </>
    );
};

export default UserDetails;
