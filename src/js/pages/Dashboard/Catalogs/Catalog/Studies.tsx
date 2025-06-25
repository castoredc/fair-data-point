import React, { Component } from 'react';
import StudiesDataTable from 'components/DataTable/StudiesDataTable';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import PageButtons from 'components/Layout/PageButtons';

import * as H from 'history';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface StudiesProps {
    catalog: string;
    history: H.History;
}

class Studies extends Component<StudiesProps> {
    render() {
        const { catalog, history } = this.props;

        return (
            <PageBody>
                <PageButtons>
                    <Button
                        startIcon={<AddIcon />}
                        onClick={() => history.push('/dashboard/catalogs/' + catalog + '/studies/add')}
                        variant="contained"
                    >
                        Add study
                    </Button>
                </PageButtons>

                <StudiesDataTable history={history} catalog={catalog} />
            </PageBody>
        );
    }
}

export default Studies;