import React, {Component} from 'react'
import {Col, Row} from "react-bootstrap";
import Toggle from "../Toggle";
import Icon from "../Icon";
import Container from "react-bootstrap/Container";
import TripleGroup from "./TripleGroup";
import {Button} from "@castoredc/matter";
import Highlight from "../Highlight";

export default class DataModelModulePreview extends Component {
    render() {
        const { title, order, rdf } = this.props;

        return <div className="DataModelModulePreview">
            <Toggle title={`Module ${order}. ${title}`}>
                <Highlight content={rdf} />
            </Toggle>
        </div>;
    }
}