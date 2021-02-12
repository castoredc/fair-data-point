import React, {Component} from "react";
import axios from "axios";
import {classNames, localizedText} from "../../../util";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import StudyList from "../../../components/List/StudyList";
import {Button, ButtonGroup, Heading, Stack} from "@castoredc/matter";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import DropdownButton from "../../../components/DropdownButton";
import DatasetList from "../../../components/List/DatasetList";
import MetadataSideBar from "../../../components/MetadataSideBar";
import StudyFilters from "../../../components/Filters/StudyFilters";
import {Sticky, StickyContainer} from 'react-sticky';

export default class Catalog extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingFDP: true,
            hasLoadedFDP: false,
            isLoadingCatalog: true,
            hasLoadedCatalog: false,
            fdp: null,
            catalog: null,
            view: 'studies',
            sideBarView: 'filter',
            representation: 'list',
            displaySideBar: false,
            appliedFilters: {},
            mobile: true,
        };

        this.datasetsRef = React.createRef();
    }

    componentDidMount() {
        window.addEventListener("resize", this.resize.bind(this));
        this.resize();

        this.getFDP();
        this.getCatalog();
    }

    resize() {
        const {mobile, displaySideBar, sideBarView} = this.state;

        const isMobile = window.innerWidth <= 767;

        this.setState({
            displaySideBar: isMobile ? (mobile ? displaySideBar : false) : true,
            sideBarView: isMobile ? 'filter' : sideBarView,
            mobile: isMobile
        });
    }

    getFDP = () => {
        axios.get('/api/fdp')
            .then((response) => {
                this.setState({
                    fdp: response.data,
                    isLoadingFDP: false,
                    hasLoadedFDP: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingFDP: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getCatalog = () => {
        axios.get('/api/catalog/' + this.props.match.params.catalog)
            .then((response) => {
                this.setState({
                    catalog: response.data,
                    isLoadingCatalog: false,
                    hasLoadedCatalog: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalog: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the catalog information';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    changeView = (view) => {
        const { sideBarView } = this.state;

        this.setState({
            view: view,
            sideBarView: view === 'datasets' ? 'metadata' : sideBarView,
        });
    };

    changeSideBarView = (view) => {
        this.setState({
            sideBarView: view,
        });
    };

    changeRepresentation = (representation) => {
        this.setState({
            representation: representation,
        });
    };

    toggleFilter = () => {
        this.setState({
            displaySideBar: !this.state.displaySideBar
        });
    };

    handleFilter = (filters) => {
        this.setState({
            appliedFilters: filters,
        });
    };

    render() {
        const {fdp, catalog, view, representation, displaySideBar, isLoadingFDP, isLoadingCatalog, mobile, appliedFilters, sideBarView} = this.state;
        const {user, embedded, location} = this.props;

        const breadcrumbs = getBreadCrumbs(location, {fdp, catalog});

        const title = catalog ? localizedText(catalog.metadata.title, 'en') : null;

        const allowFilterOverlay = mobile;

        return <Layout
            className="Catalog"
            title={title}
            isLoading={(isLoadingFDP || isLoadingCatalog)}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title}/>

            <MainBody isLoading={(isLoadingFDP || isLoadingCatalog)}>
                {catalog && <StickyContainer className="StickyContainer">
                    <div className="MainCol">
                        {(catalog.metadata.description && !embedded) && <div className="InformationDescription">
                            {localizedText(catalog.metadata.description, 'en', true)}
                        </div>}

                        <div className="DatasetsHeader" ref={this.datasetsRef}>
                            <Stack distribution="equalSpacing">
                                <div className="DatasetsHeaderTitle">
                                    <DropdownButton
                                        className="SubsectionSelector"
                                        text={view === 'studies' ? 'Studies' : 'Datasets'}
                                        items={view === 'studies' ? [{
                                            destination: () => {this.changeView('datasets')},
                                            label: 'Datasets'
                                        }] : [{
                                            destination: () => {this.changeView('studies')},
                                            label: 'Studies'
                                        }]}
                                    />
                                </div>
                                {view === 'studies' && <div className="DatasetHeaderButtons">
                                    <ButtonGroup className="ToggleButton">
                                        <Button buttonType={representation === 'list' ? 'primary' : 'secondary'}
                                                onClick={() => this.changeRepresentation('list')}
                                                active={representation === 'list'}>
                                            List
                                        </Button>
                                        <Button buttonType={representation === 'map' ? 'primary' : 'secondary'}
                                                onClick={() => this.changeRepresentation('map')}
                                                active={representation === 'map'}>
                                            Map
                                        </Button>
                                    </ButtonGroup>
                                    <div className={classNames('FilterButton', !allowFilterOverlay && 'Hidden')}>
                                        <Button icon="filters" onClick={this.toggleFilter}>
                                            Filters
                                        </Button>
                                    </div>
                                </div>}
                            </Stack>
                        </div>

                        <StudyList
                            visible={view === 'studies'}
                            representation={representation}
                            state={breadcrumbs.current ? breadcrumbs.current.state : null}
                            catalog={catalog}
                            embedded={embedded}
                            appliedFilters={appliedFilters}
                        />

                        <DatasetList
                            visible={view === 'datasets'}
                            catalog={catalog}
                            state={breadcrumbs.current ? breadcrumbs.current.state : null}
                            embedded={embedded}
                        />
                    </div>

                    <div className={classNames('SideCol', !displaySideBar && 'Hidden', allowFilterOverlay && 'Overlay')}>
                        <Sticky>
                            {({style, isSticky}) => {
                                return <div
                                    className={classNames('StickySideCol', (!allowFilterOverlay && isSticky) && 'Sticky')}
                                    style={allowFilterOverlay ? undefined : style}
                                >
                                    {allowFilterOverlay ? <div className="OverlayHeader">
                                        <Heading type="Subsection">Search studies</Heading>
                                        <Button icon="cross" className="CloseButton" onClick={this.toggleFilter} iconDescription="Close" />
                                    </div> : <div>
                                        {view === 'datasets' ? <Heading type="Subsection">Metadata</Heading> : <DropdownButton
                                            className="SubsectionSelector"
                                            text={sideBarView === 'filter' ? 'Search studies' : 'Metadata'}
                                            items={sideBarView === 'filter' ? [{
                                                destination: () => {
                                                    this.changeSideBarView('metadata')
                                                },
                                                label: 'Metadata'
                                            }] : [{
                                                destination: () => {
                                                    this.changeSideBarView('filter')
                                                },
                                                label: 'Search studies'
                                            }]}
                                        />}
                                    </div>}

                                    <div className={classNames('SideColItem', sideBarView !== 'metadata' && 'Hidden')}>
                                        <MetadataSideBar type="catalog" metadata={catalog.metadata}/>
                                    </div>

                                    <div className={classNames('SideColItem', sideBarView !== 'filter' && 'Hidden')}>
                                        <StudyFilters catalog={catalog} onFilter={(filter) => this.handleFilter(filter)} />
                                    </div>

                                    {allowFilterOverlay && <div className="OverlayFooter">
                                        <Button fullWidth onClick={this.toggleFilter}>View studies</Button>
                                    </div>}
                                </div>
                            }}
                        </Sticky>
                    </div>
                </StickyContainer>}
            </MainBody>
        </Layout>;
    }
}