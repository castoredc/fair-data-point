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

        const url = '/admin/catalog/' + catalog + '/dataset/' + slug;

        return <Row className="ListItem AdminStudyListItem AdminListItem">
            <Col md={7} className="AdminStudyListItemTitle AdminListItemTitle">
                <Link to={url}>
                    {name}
                </Link>
            </Col>
            <Col md={1} className="AttributeCheckmark">
                {consent.socialMedia && <Link to={url}><Icon type="checkmark" /></Link>}
            </Col>
            <Col md={1} className="AttributeCheckmark">
                {consent.publish && <Link to={url}><Icon type="checkmark" /></Link>}
            </Col>
            <Col md={1} className="AttributeCheckmark">
                {published && <Link to={url}><Icon type="checkmark" /></Link>}
            </Col>
            <Col md={2} className="AdminStudyListItemButtons AdminListItemButtons">
                <Link to={'/fdp/' + catalog + '/' + slug} target="_blank">
                    <Button variant="link">
                        <Icon type="view" />
                    </Button>
                </Link>
                <Link to={url}>
                    <Button variant="link">
                        <Icon type="edit" />
                    </Button>
                </Link>
            </Col>
        </Row>;
    }
}