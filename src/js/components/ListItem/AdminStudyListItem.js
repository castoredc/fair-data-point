import React, {Component} from 'react'
import {Link} from "react-router-dom";
import './StudyListItem.scss'
import {RecruitmentStatus} from "../MetadataItem/EnumMappings";
import Tags from "../Tags";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Icon from '../Icon';
import Button from "react-bootstrap/Button";

export default class AdminStudyListItem extends Component {
    render() {
        const { id, name, catalog, slug}  = this.props;

        return <Row className="ListItem AdminStudyListItem">
            <Col md={8} className="AdminStudyListItemTitle">
                <Link to={'/fdp/' + catalog + '/' + slug} target="_blank">
                    {name}
                </Link>
            </Col>
            <Col md={4} className="AdminStudyListItemButtons">
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