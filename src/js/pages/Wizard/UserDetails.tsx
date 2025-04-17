import React from 'react';
import Emoji from 'components/Emoji';
import { AuthorizedRouteComponentProps } from 'components/Route';
import UserDetailsForm from 'components/Form/Agent/UserDetailsForm';
import { WizardHeader } from 'components/Layout/WizardLayout';
import { Box } from '@mui/material';

interface UserDetailsProps extends AuthorizedRouteComponentProps {
}

const UserDetails: React.FC<UserDetailsProps> = ({ user, history }) => {
    if (user === null) {
        return null;
    }

    return (
        <>
            <WizardHeader
                title={
                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                        <Emoji symbol="👋" />
                        <Box component="span" sx={{ ml: 1 }}>
                            Hi {user.details ? user.details.firstName : ''}!
                        </Box>
                    </Box>
                }
                description="Before you continue, please check your details below."
            />
            <UserDetailsForm user={user} history={history} />
        </>
    );
};

export default UserDetails;
