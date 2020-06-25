import React, {Component} from 'react';
import {classNames, localizedText} from "../../util";
import DocumentTitle from "../DocumentTitle";
import {Container, Row} from "react-bootstrap";
import {Link} from "react-router-dom";
import '../../pages/Main/Main.scss';
import Breadcrumbs from "../Breadcrumbs";
import Col from "react-bootstrap/Col";
import './Header.scss';
import {Button, CastorLogo, Heading} from "@castoredc/matter";

export default class Header extends Component {
    constructor(props) {
        super(props);

        this.state = {
            mobile: null,
            smallHeader: false
        };
    }

    componentDidMount() {
        window.addEventListener("resize", this.resize.bind(this));
        window.addEventListener("scroll", this.scroll.bind(this));
        this.resize();
    }

    componentWillUnmount(){
        window.removeEventListener('resize', this.resize.bind(this));
        window.removeEventListener('scroll', this.scroll.bind(this));
    }

    resize() {
        this.setState({mobile: window.innerWidth <= 767});
    }

    scroll() {
        const distanceY = window.pageYOffset || document.documentElement.scrollTop;
        const shrinkOn = 100;

        this.setState({smallHeader: distanceY > shrinkOn});
    }

    render() {
        const { embedded, className, title, badge, location, data, breadcrumbs } = this.props;
        const { mobile, smallHeader } = this.state;

        return <header className={classNames(className, embedded && 'Embedded', mobile ? 'Mobile' : 'Desktop')}>
            <DocumentTitle title={title}/>
            {!embedded && <div className="Header">
                <div className="Spacing" />
                {! mobile && <div className={classNames('MainHeader', smallHeader && 'Small')}>
                    <Container>
                        <Row>
                            <Col md={8} className="HeaderLogoCol">
                                <Link to="/fdp">
                                    <CastorLogo className="Logo" />
                                </Link>
                            </Col>
                            <Col md={4} className="HeaderUserCol">
                                {/*<Button></Button>*/}
                            </Col>
                        </Row>
                    </Container>
                </div>}
                {(! mobile && breadcrumbs) && <Breadcrumbs breadcrumbs={breadcrumbs.crumbs} />}
                {mobile && <div className="MobileHeader">
                    <Container>
                        <Row>
                            <Col className="HeaderBackCol">
                                {(breadcrumbs && breadcrumbs.previous) && <Link to={{
                                    pathname: breadcrumbs.previous.path,
                                    state: breadcrumbs.previous.state
                                }}><Button
                                    icon="arrowLeft"
                                    iconDescription={`Go back to ${localizedText(breadcrumbs.previous.title, 'en')}`}
                                /></Link>}
                            </Col>
                            <Col className="HeaderLogoCol">
                                <Link to="/fdp">
                                    <CastorLogo className="Logo" />
                                </Link>
                            </Col>
                            <Col className="HeaderUserCol">
                                {/*<Button></Button>*/}
                            </Col>
                        </Row>
                    </Container>
                </div>}
            </div>}
            <div className="InformationHeader">
                <Container className="Children">
                    <Row>
                        <Col md={8}>
                            <Heading type="Section" level="1">
                                {title}
                                {badge && <span className="InformationBadge">{badge}</span>}
                            </Heading>
                        </Col>
                        <Col md={4}>
                        </Col>
                    </Row>
                </Container>
            </div>
        </header>;
    }
}