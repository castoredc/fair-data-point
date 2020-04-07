import React, {Component} from 'react'
import {Link} from "react-router-dom";
import './StudyListItem.scss'
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Icon from '../Icon';
import Button from "react-bootstrap/Button";

export default class AdminStudyListItem extends Component {
    render() {
        const { id, name, catalog, slug, published, consent}  = this.props;

        return <Row className="ListItem AdminStudyListItem">
            <Col md={6} className="AdminStudyListItemTitle">
                <Link to={'/fdp/' + catalog + '/' + slug} target="_blank">
                    {name}
                </Link>
            </Col>
            <Col md={1} className="AttributeCheckmark">
                {consent.socialMedia && <Link to={'/fdp/' + catalog + '/' + slug} target="_blank"><Icon type="checkmark" /></Link>}
            </Col>
            <Col md={1} className="AttributeCheckmark">
                {consent.publish && <Link to={'/fdp/' + catalog + '/' + slug} target="_blank"><Icon type="checkmark" /></Link>}
            </Col>
            <Col md={1} className="AttributeCheckmark">
                {published && <Link to={'/fdp/' + catalog + '/' + slug} target="_blank"><Icon type="checkmark" /></Link>}
            </Col>
            <Col md={3} className="AdminStudyListItemButtons">
                <Link to={'/fdp/' + catalog + '/' + slug} target="_blank">
                    <Button variant="link">
                        <Icon type="view" /> View
                    </Button>
                </Link>
                <Link to={'/admin/' + catalog + '/study/' + id + '/metadata/update/details'}>
                    <Button variant="link">
                        <Icon type="edit" /> Edit
                    </Button>
                </Link>
            </Col>
        </Row>;
    }
}