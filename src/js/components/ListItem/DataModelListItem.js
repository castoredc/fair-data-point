import React, {Component} from 'react'
import {Link} from "react-router-dom";
import './StudyListItem.scss'
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Icon from '../Icon';
import Button from "react-bootstrap/Button";

export default class DataModelListItem extends Component {
    render() {
        const { id, name }  = this.props;

        const url = '/admin/model/' + id;

        return <Row className="ListItem AdminStudyListItem AdminListItem">
            <Col md={10} className="AdminStudyListItemTitle AdminListItemTitle">
                <Link to={url}>
                    {name}
                </Link>
            </Col>
            <Col md={2} className="AdminStudyListItemButtons AdminListItemButtons">
                {/*<Link to={'/model/' + catalog + '/' + slug} target="_blank">*/}
                {/*    <Button variant="link">*/}
                {/*        <Icon type="view" />*/}
                {/*    </Button>*/}
                {/*</Link>*/}
                <Link to={url}>
                    <Button variant="link">
                        <Icon type="edit" />
                    </Button>
                </Link>
            </Col>
        </Row>;
    }
}