import React, {Component} from 'react'
import './StudyListItem.scss'
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";

export default class AdminPrefixListItem extends Component {
    render() {
        const { id, prefix, uri}  = this.props;

        return <Row className="ListItem AdminPrefixListItem AdminListItem">
            <Col md={4} className="AdminPrefixListItemPrefix AdminListItemTitle">
                <div>{prefix}</div>
            </Col>
            <Col md={6} className="AdminPrefixListItemUri AdminPrefixListItemContent">
                <div>{uri}</div>
            </Col>
            <Col md={2} className="AdminStudyListItemButtons AdminListItemButtons">
                {/*<Button variant="link">*/}
                {/*    <Icon type="edit" />*/}
                {/*</Button>*/}
            </Col>
        </Row>;
    }
}