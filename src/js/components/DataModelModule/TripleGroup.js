import React, {Component} from 'react'
import {ActionMenu, Icon} from "@castoredc/matter";

export default class TripleGroup extends Component {
    render() {
        const {id, type, title, repeated, value, predicates, openTripleModal, openRemoveTripleModal} = this.props;

        const newData = {
            subjectType: type,
            subjectValue: id,
        };

        return <div className="DataModelTriple">
            <div className="DataModelSubject">
                {Node(title, type, value, repeated)}
            </div>

            <div className="DataModelPredicateObjects">
                {predicates.map((predicate) => {
                    return <TriplePredicate
                        key={predicate.id}
                        id={predicate.id}
                        value={predicate.value}
                        objects={predicate.objects}
                        data={newData}
                        openTripleModal={openTripleModal}
                        openRemoveTripleModal={openRemoveTripleModal}
                    />;
                })}
            </div>
        </div>;
    }
}

export class TriplePredicate extends Component {
    render() {
        const {id, value, objects, data, openTripleModal, openRemoveTripleModal} = this.props;

        const newData = {
            ...data,
            predicateValue: value.value,
        };

        return <div className="TriplePredicateObject">
            <div className="DataModelPredicate">
                {value.prefixedValue ? value.prefixedValue : value.value}
            </div>

            <div className="DataModelObjects">
                {objects.map((object) => {
                    return <TripleObject
                        key={object.id}
                        id={object.id}
                        type={object.type}
                        title={object.title}
                        description={object.description}
                        value={object.value}
                        repeated={object.repeated}
                        data={newData}
                        tripleId={object.tripleId}
                        openTripleModal={openTripleModal}
                        openRemoveTripleModal={openRemoveTripleModal}
                    />;
                })}
            </div>
        </div>;
    }
}

export class TripleObject extends Component {
    constructor(props) {
        super(props);
        this.ref = React.createRef();
    }

    render() {
        const {tripleId, id, type, title, repeated, value, openTripleModal, data, openRemoveTripleModal} = this.props;

        const newData = {
            ...data,
            objectType: type,
            objectValue: id,
            id: tripleId
        };

        return <div className="DataModelObject">
            {Node(title, type, value, repeated)}

            <div className="DataModelTripleActions" ref={this.ref}>
                <ActionMenu
                    accessibleLabel="Contextual menu"
                    // container={this.ref.current}
                    container={this.ref.current}
                    items={[
                        {
                            destination: () => {
                                openTripleModal(newData)
                            },
                            label: 'Edit triple'
                        },
                        {
                            destination: () => {
                                openRemoveTripleModal(newData)
                            },
                            label: 'Delete triple'
                        }
                    ]}
                />
            </div>
        </div>;
    }
}

export const Node = (title, type, value, repeated) => {
    return <div>
        {title}
        {(type === "internal") && <div className="NodeInfo Slug">
            {repeated ? <span>/{value}/[instance_id]</span> : <span>/{value}</span>}
        </div>}
        {(type === "external") && <div className="NodeInfo PrefixedUri">
            <span>
                {value.prefixedValue !== null ? value.prefixedValue : `...:${value.base}`}
                &nbsp;<Icon type="openNewWindow" width="8px" height="8px"/>
            </span>
        </div>}
        {(type === "value") && <div className="NodeInfo Value">
            <span>
                {value.value === 'annotated' ? 'Annotated value' : `Plain value (${value.dataType})`}
                {repeated && ' - repeated'}
            </span>
        </div>}
        {(type === "literal") && <div className="NodeInfo Literal">
            <span>
                {value.value} ({value.dataType})
            </span>
        </div>}
    </div>;
};