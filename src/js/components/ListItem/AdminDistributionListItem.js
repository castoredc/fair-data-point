import React, {Component} from 'react'
import {Link} from "react-router-dom";
import './StudyListItem.scss'
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Icon from '../Icon';
import Button from "react-bootstrap/Button";

export default class AdminDistributionListItem extends Component {
    render() {
        const { id, name, catalog, dataset, slug, type}  = this.props;

        return <Row className="ListItem AdminDistributionListItem AdminListItem">
            <Col md={8} className="AdminStudyListItemTitle AdminListItemTitle">
                <Link to={'/fdp/' + catalog + '/' + dataset + '/' + slug} target="_blank">
                    {name}
                </Link>
            </Col>
            <Col md={2} className="AdminListItemType">
                <Link to={'/fdp/' + catalog + '/' + dataset + '/' + slug} target="_blank">
                    {type.toUpperCase()}
                </Link>
            </Col>
            <Col md={2} className="AdminStudyListItemButtons AdminListItemButtons">
                <Link to={'/fdp/' + catalog + '/' + dataset + '/' + slug} target="_blank">
                    <Button variant="link">
                        <Icon type="view" />
                    </Button>
                </Link>
                <Link to={'/admin/' + catalog + '/dataset/' + dataset + '/distribution/' + slug}>
                    <Button variant="link">
                        <Icon type="edit" />
                    </Button>
                </Link>
            </Col>
        </Row>;
    }
}