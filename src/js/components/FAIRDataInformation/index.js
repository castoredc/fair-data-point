import React, {Component} from 'react';
import {classNames} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Container, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import Logo from '../Logo';

export default class FAIRDataInformation extends Component {
    render() {
        const {embedded, className, title, description, logo = '', children} = this.props;

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
                            {logo !== '' && <div className="Logo">
                                <img src={logo} alt={title + ' logo'}/>
                            </div>}
                            <h1 className="Title">{title}</h1>
                            <div className="Description">{description}</div>
                        </div>
                    </div>
                </Container>
            </div>}
            <div className="Information">
                <Row className="InformationRow">
                    <Container className="Children Datasets">
                        {children}
                    </Container>
                </Row>
            </div>
        </div>;
    }
}