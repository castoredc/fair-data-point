import React, {Component} from "react";
import axios from "axios";
import {classNames, localizedText} from "../../../util";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";
import StudyList from "../../../components/List/StudyList";
import {Button, ButtonGroup, Stack} from "@castoredc/matter";
import Layout from "../../../components/Layout";
import Header from "../../../components/Layout/Header";
import MainBody from "../../../components/Layout/MainBody";
import {getBreadCrumbs} from "../../../utils/BreadcrumbUtils";
import DropdownButton from "../../../components/DropdownButton";
import DatasetList from "../../../components/List/DatasetList";

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
            representation: 'list',
            displayFilter: false,
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
        const {mobile, displayFilter} = this.state;

        const isMobile = window.innerWidth <= 767;

        this.setState({
            displayFilter: isMobile ? (mobile ? displayFilter : false) : true,
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
        this.setState({
            view: view,
            // displayFilter: displayList && (window.innerWidth > 767)
        });
    };

    changeRepresentation = (representation) => {
        this.setState({
            representation: representation,
            // displayFilter: displayList && (window.innerWidth > 767)
        });
    };

    toggleFilter = () => {
        this.setState({
            displayFilter: !this.state.displayFilter
        });
    };

    render() {
        const {fdp, catalog, view, representation, displayFilter, isLoadingFDP, isLoadingCatalog, mobile} = this.state;
        const {user, embedded, location} = this.props;

        const breadcrumbs = getBreadCrumbs(location, {fdp, catalog});

        const title = catalog ? localizedText(catalog.metadata.title, 'en') : null;

        const showFilterButton = view === 'studies' ? (mobile ? true : representation === 'map') : false;

        return <Layout
            className="Catalog"
            title={title}
            isLoading={(isLoadingFDP || isLoadingCatalog)}
            embedded={embedded}
        >
            <Header user={user} embedded={embedded} breadcrumbs={breadcrumbs} title={title}/>

            <MainBody isLoading={(isLoadingFDP || isLoadingCatalog)}>
                {(catalog && catalog.metadata.description && !embedded) && <div className="MainCol">
                    <div className="InformationDescription">
                        {localizedText(catalog.metadata.description, 'en', true)}
                    </div>
                </div>}

                <div className={classNames('DatasetsHeader', 'MainCol', representation === 'map' && 'FullWidth')}
                     ref={this.datasetsRef}>
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
                            <div className={classNames('FilterButton', !showFilterButton && 'Hidden')}>
                                <Button icon="filters" onClick={this.toggleFilter} isDropdown isOpen={displayFilter}>
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
                    displayFilter={displayFilter}
                />

                <DatasetList
                    visible={view === 'datasets'}
                    catalog={catalog}
                    state={breadcrumbs.current ? breadcrumbs.current.state : null}
                    embedded={embedded}
                    className="MainCol"
                />
            </MainBody>
        </Layout>;
    }
}