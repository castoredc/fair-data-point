import React, {Component} from 'react';
import {classNames, localizedText} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Container, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import Logo from '../Logo';
import './FAIRDataInformation.scss';
import Breadcrumbs from "../Breadcrumbs";
import Breadcrumb from "../Breadcrumbs/Breadcrumb";

export default class FAIRDataInformation extends Component {
    render() {
        const {embedded, className, title, badge, breadcrumbs, license, version, issued, modified, children} = this.props;

        const catalog = breadcrumbs.catalog || null;
        const study = breadcrumbs.study || null;
        const dataset = breadcrumbs.dataset || null;
        const distribution = breadcrumbs.distribution || null;
        const query = breadcrumbs.query || null;

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
                {breadcrumbs && <Breadcrumbs>
                    <Breadcrumb to="/fdp">
                        FAIR Data Point
                    </Breadcrumb>

                    {breadcrumbs.catalog && <Breadcrumb to={{
                        pathname: breadcrumbs.catalog.relativeUrl,
                        state: {
                            catalog: catalog,
                        },
                    }}>
                        {localizedText(breadcrumbs.catalog.metadata.title, 'en')}
                    </Breadcrumb>}
                    {breadcrumbs.study && <Breadcrumb to={{
                        pathname: `/study/${breadcrumbs.study.slug}`,
                        state: {
                            catalog: catalog,
                            study: study,
                        },
                    }}>
                        {breadcrumbs.study.metadata.briefName}
                    </Breadcrumb>}
                    {breadcrumbs.dataset && <Breadcrumb to={{
                        pathname: breadcrumbs.dataset.relativeUrl,
                        state: {
                            catalog: catalog,
                            study: study,
                            dataset: dataset,
                        },
                    }}>
                        {localizedText(breadcrumbs.dataset.metadata.title, 'en')}
                    </Breadcrumb>}
                    {breadcrumbs.distribution && <Breadcrumb to={{
                        pathname: breadcrumbs.distribution.relativeUrl,
                        state: {
                            catalog: catalog,
                            study: study,
                            dataset: dataset,
                            distribution: distribution,
                        },
                    }}>
                        {localizedText(breadcrumbs.distribution.metadata.title, 'en')}
                    </Breadcrumb>}
                    {(breadcrumbs.query && breadcrumbs.distribution) && <Breadcrumb to={{
                        pathname: breadcrumbs.distribution.relativeUrl + '/query',
                        state: {
                            catalog: catalog,
                            study: study,
                            dataset: dataset,
                            distribution: distribution,
                            query: query
                        },
                    }}>
                        Query
                    </Breadcrumb>}
                </Breadcrumbs>}
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