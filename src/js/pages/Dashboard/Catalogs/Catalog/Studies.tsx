import React, { Component } from 'react';
import StudiesDataTable from 'components/DataTable/StudiesDataTable';
import Button from '@mui/material/Button';
import AddIcon from '@mui/icons-material/Add';
import Stack from '@mui/material/Stack';

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
                <div className="PageButtons">
                    <Stack direction="row" sx={{ justifyContent: 'flex-end' }}>
                        <Button
                            startIcon={<AddIcon />}
                            className="AddButton"
                            onClick={() => history.push('/dashboard/catalogs/' + catalog + '/studies/add')}
                            variant="contained"
                        >
                            Add study
                        </Button>
                    </Stack>
                </div>

                <StudiesDataTable history={history} catalog={catalog} />
            </PageBody>
        );
    }
}

export default Studies;