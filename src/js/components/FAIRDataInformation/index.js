import React, {Component} from 'react';
import {classNames} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Container, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import Logo from '../Logo';
import './FAIRDataInformation.scss';

export default class FAIRDataInformation extends Component {
    render() {
        const {embedded, className, title, badge, logo = '', license, version, issued, modified, children} = this.props;
        return <div className={classNames(className, 'TopLevelContainer', embedded && 'Embedded')}>
            <DocumentTitle title={title}/>

            {!embedded && <div className="Header">
                <div className="MainHeader">
                    <Container>
                        <Link to="/fdp">
                            <Logo />
                        </Link>
                    </Container>
                </div>
                <Container>
                    <div className="InformationHeader">
                        <div className="InformationHeaderTop">
                            <h1 className="Title">
                                {title}
                                {badge && <span className="InformationBadge">{badge}</span>}
                            </h1>
                        </div>
                    </div>
                </Container>
            </div>}
            <div className="Information">
                <Row className="InformationRow">
                    <Container className="Children">
                        {children}
                    </Container>
                </Row>
            </div>
            {/*{!embedded && <div className="Footer">*/}
            {/*    <Container>*/}
            {/*        <Row>*/}
            {/*            <Col sm={6} md={3}>{version && <MetadataItem label="Version" value={version} />}</Col>*/}
            {/*            <Col sm={6} md={3}>{issued && <MetadataItem label="Issued" value={issued} type="date" />}</Col>*/}
            {/*            <Col sm={6} md={3}>{modified && <MetadataItem label="Modified" value={modified} type="date" />}</Col>*/}
            {/*            <Col sm={6} md={3}>{license && <MetadataItem label="License" url={license.url} value={license.name} />}</Col>*/}
            {/*        </Row>*/}
            {/*    </Container>*/}
            {/*</div>}*/}
        </div>;
    }
}