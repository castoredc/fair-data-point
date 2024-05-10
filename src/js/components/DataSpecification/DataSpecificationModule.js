import React, { Component } from 'react';
import TripleGroup from './TripleGroup';
import { Button, Stack } from '@castoredc/matter';

export default class DataSpecificationModule extends Component {
    render() {
        const { groupedTriples, openModuleModal, openTripleModal, openRemoveTripleModal, openAddModuleModal } = this.props;

        return (
            <div className="DataSpecificationModule">
                <div className="ButtonBar">
                    <Stack distribution="trailing">
                        <Button icon="edit" buttonType="secondary" onClick={openModuleModal}>
                            Edit group
                        </Button>
                        <Button
                            icon="add"
                            onClick={() => {
                                openTripleModal(null);
                            }}
                        >
                            Add triple
                        </Button>
                    </Stack>
                </div>

                <div className="DataSpecificationTable LargeTable">
                    <div className="DataSpecificationTableHeader TableHeader">
                        <div>Subject</div>
                        <div>Predicate</div>
                        <div>Object</div>
                    </div>

                    {groupedTriples.length === 0 ? (
                        <div className="NoResults">This group does not contain triples.</div>
                    ) : (
                        <div className="DataSpecificationTableBody TableBody">
                            {groupedTriples.map(element => {
                                return (
                                    <TripleGroup
                                        key={element.id}
                                        id={element.id}
                                        type={element.type}
                                        title={element.title}
                                        repeated={element.repeated}
                                        description={element.description}
                                        value={element.value}
                                        predicates={element.predicates}
                                        openTripleModal={openTripleModal}
                                        openRemoveTripleModal={openRemoveTripleModal}
                                    />
                                );
                            })}
                        </div>
                    )}
                </div>
            </div>
        );
    }
}
