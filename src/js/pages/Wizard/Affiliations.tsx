import React, { Component } from 'react';
import Emoji from 'components/Emoji';
import queryString from 'query-string';
import AffiliationsForm from 'components/Form/Agent/AffiliationsForm';
import { AuthorizedRouteComponentProps } from 'components/Route';
import { WizardHeader } from 'components/Layout/WizardLayout';
import { Box } from '@mui/material';

interface AffiliationsProps extends AuthorizedRouteComponentProps {
}

interface AffiliationsState {
    isSaved: boolean;
}

class Affiliations extends Component<AffiliationsProps, AffiliationsState> {
    constructor(props) {
        super(props);

        this.state = {
            isSaved: false,
        };
    }

    handleSave = () => {
        this.setState({
            isSaved: true,
        });
    };

    render() {
        const { location, user } = this.props;
        const { isSaved } = this.state;

        if (!(user && user.details)) {
            return null;
        }

        if (isSaved) {
            const params = queryString.parse(location.search);
            window.location.href = typeof params.origin !== 'undefined' ? String(params.origin) : '/';
        }

        return (
            <>
                <WizardHeader
                    title={
                        <Box sx={{ display: 'flex', alignItems: 'center' }}>
                            <Emoji symbol="🏥" />
                            <Box component="span" sx={{ ml: 1 }}>
                                Where do you work, {user.details.firstName}?
                            </Box>
                        </Box>
                    }
                    description="Please add your affiliation(s) below."
                />
                <AffiliationsForm user={user} onSaved={this.handleSave} />
            </>
        );
    }
}

export default Affiliations;