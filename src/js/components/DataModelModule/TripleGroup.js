import React, {Component} from 'react'
import {Col, Row} from "react-bootstrap";
import {ActionMenu, Icon} from "@castoredc/matter";
import TripleModal from "../../modals/TripleModal";
import ConfirmModal from "../../modals/ConfirmModal";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";

export default class TripleGroup extends Component {
    render() {
        const { id, type, title, description, value, predicates, openTripleModal, openRemoveTripleModal } = this.props;

        const newData = {
            subjectType: type,
            subjectValue: id,
        };

        return <Row className="DataModelTriple">
            <Col sm={4}>
                {Node(title, type, value)}
            </Col>
            <Col sm={8}>
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
            </Col>
        </Row>;
    }
}

export class TriplePredicate extends Component {
    render() {
        const { id, value, objects, data, openTripleModal, openRemoveTripleModal } = this.props;

        const newData = {
            ...data,
            predicateValue: value.value,
        };

        return <Row className="TriplePredicate">
            <Col sm={6}>
                {value.prefixedValue ? value.prefixedValue : value.value}
            </Col>
            <Col sm={6}>
                {objects.map((object) => {
                    return <TripleObject
                        key={object.id}
                        id={object.id}
                        type={object.type}
                        title={object.title}
                        description={object.description}
                        value={object.value}
                        data={newData}
                        tripleId={object.tripleId}
                        openTripleModal={openTripleModal}
                        openRemoveTripleModal={openRemoveTripleModal}
                    />;
                })}
            </Col>
        </Row>;
    }
}

export class TripleObject extends Component {
    constructor(props) {
        super(props);
        this.ref = React.createRef();
    }

    render() {
        const { tripleId, id, type, title, description, value, openTripleModal, data, openRemoveTripleModal } = this.props;

        const newData = {
            ...data,
            objectType: type,
            objectValue: id,
            id: tripleId
        };

        return <div className="TripleObject">
            {Node(title, type, value)}

            <div className="TripleActions" ref={this.ref}>
                <ActionMenu
                    accessibleLabel="Contextual menu"
                    // container={this.ref.current}
                    container={this.ref.current}
                    items={[
                        {
                            destination: () => {openTripleModal(newData)},
                            label: 'Edit triple'
                        },
                        {
                            destination: () => {openRemoveTripleModal(newData)},
                            label: 'Delete triple'
                        }
                    ]}
                />
            </div>
        </div>;
    }
}

export const Node = (title, type, value) => {
    return <div>
        {title}
        {(type === "internal") && <div className="NodeInfo Slug">
            <span>/{value}</span>
        </div>}
        {(type === "external") && <div className="NodeInfo PrefixedUri">
            <span>
                {value.prefixedValue !== null ? value.prefixedValue : `...:${value.base}`}
                &nbsp;<Icon type="openNewWindow" width="8px" height="8px" />
            </span>
        </div>}
        {(type === "value") && <div className="NodeInfo Value">
            <span>
                {value.value === 'annotated' ? 'Annotated value' : `Plain value (${value.dataType})`}
            </span>
        </div>}
        {(type === "literal") && <div className="NodeInfo Literal">
            <span>
                {value.value} (${value.dataType})
            </span>
        </div>}
    </div>;
};