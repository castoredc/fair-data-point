import React, { Component } from 'react';
import StudiesDataTable from 'components/DataTable/StudiesDataTable';
import { Button, Stack } from '@castoredc/matter';
import * as H from 'history';
import PageBody from 'components/Layout/Dashboard/PageBody';

interface StudiesProps {
    catalog: string;
    history: H.History;
}

export default class Studies extends Component<StudiesProps> {
    render() {
        const { catalog, history } = this.props;

        return (
            <PageBody>
                <div className="PageButtons">
                    <Stack distribution="trailing" alignment="end">
                        <Button icon="add" className="AddButton" onClick={() => history.push('/dashboard/catalogs/' + catalog + '/studies/add')}>
                            Add study
                        </Button>
                    </Stack>
                </div>

                <StudiesDataTable history={history} catalog={catalog} />
            </PageBody>
        );
    }
}
