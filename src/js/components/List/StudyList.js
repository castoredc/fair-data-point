import React, {Component} from "react";
import axios from "axios";
import {classNames} from "../../util";
import StudyListItem from "../ListItem/StudyListItem";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import StudiesMap from "../Map/StudiesMap";
import {Button, Heading, Pagination} from "@castoredc/matter";
import InlineLoader from "../LoadingScreen/InlineLoader";
import DataGridHelper from "../DataTable/DataGridHelper";
import {Sticky, StickyContainer} from "react-sticky";
import StudyFilters from "../Filters/StudyFilters";

export default class StudyList extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isLoadingStudies: true,
            isLoadingMap: true,
            showMap: false,
            studies: null,
            map: null,
            pagination: {
                currentPage: 1,
                start: 1,
                perPage: props.embedded ? 5 : 10,
                totalResults: null,
                totalPages: null
            },


            representation: 'list',
            displaySideBar: false,
            appliedFilters: {},
            mobile: true,
        };

        this.wrapperRef = React.createRef();
    }

    componentDidMount() {
        this.getStudies();
        this.getMap();

        window.addEventListener("resize", this.resize.bind(this));
        this.resize();
    }

    resize() {
        const {mobile, displaySideBar} = this.state;

        const isMobile = window.innerWidth <= 767;

        this.setState({
            displaySideBar: isMobile ? (mobile ? displaySideBar : false) : true,
            mobile: isMobile
        });
    }

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
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: 1
            },
            appliedFilters: filters,
        }, () => {
            this.getStudies();
            this.getMap();
            window.scrollTo(0, this.wrapperRef.current.offsetTop - 55);
        });
    };

    getStudies = () => {
        const {pagination, appliedFilters} = this.state;
        const {catalog, agent} = this.props;

        this.setState({
            isLoadingStudies: true,
        });

        let filters = appliedFilters;
        filters['page'] = pagination.currentPage;
        filters['perPage'] = pagination.perPage;

        let url = '';

        if (catalog) {
            url = '/api/catalog/' + catalog.slug + '/study';
        } else if (agent) {
            url = '/api/agent/details/' + agent.slug + '/study';
        }

        axios.get(url, {params: filters})
            .then((response) => {
                const studies = response.data.results.filter((study) => {
                    return study.hasMetadata
                });

                this.setState({
                    studies: studies,
                    pagination: DataGridHelper.parseResults(response.data),
                    isLoadingStudies: false
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingStudies: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the studies';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    getMap = () => {
        const {appliedFilters} = this.state;
        const {catalog, agent} = this.props;

        this.setState({
            isLoadingMap: true
        });

        let url = '';

        if (catalog) {
            url = '/api/catalog/' + catalog.slug + '/map';
        } else if (agent) {
            url = '/api/agent/details/' + agent.slug + '/map';
        }

        axios.get(url, {params: appliedFilters})
            .then((response) => {
                this.setState({
                    map: response.data,
                    isLoadingMap: false,
                    showMap: response.data.length > 0
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingMap: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the map';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    handlePagination = (paginationCount) => {
        const {pagination} = this.state;

        this.setState({
            pagination: {
                ...pagination,
                currentPage: paginationCount.currentPage,
                perPage: paginationCount.pageLimit
            }
        }, () => {
            this.getStudies();
            window.scrollTo(0, this.wrapperRef.current.offsetTop - 55);
        });
    };

    render() {
        const {
            isLoadingStudies,
            representation,
            displaySideBar,
            mobile,
            isLoadingMap,
            pagination,
            studies,
            map
        } = this.state;
        const {embedded, visible, state, catalog, agent, className} = this.props;

        const allowFilterOverlay = mobile;

        if (!visible) {
            return null;
        }

        if (studies === null || map === null) {
            return <InlineLoader/>;
        }

        return <StickyContainer className="StickyContainer">
            <div className={classNames('Studies HasFilters', className)} ref={this.wrapperRef}>
                {representation === 'list' &&
                <div className={classNames('LargeList', isLoadingStudies && 'Loading')}>
                    {studies.length > 0 ? <div>
                        {studies.map((item, index) => {
                            if (item.hasMetadata === false) {
                                return null;
                            }
                            return <StudyListItem key={index}
                                                  newWindow={embedded}
                                                  state={state}
                                                  catalog={catalog}
                                                  link={`/study/${item.slug}`}
                                                  name={item.metadata.briefName}
                                                  description={item.metadata.briefSummary}
                                                  recruitmentStatus={item.metadata.recruitmentStatus}
                                                  intervention={item.metadata.intervention}
                                                  condition={item.metadata.condition}
                            />
                        })}

                        <Pagination
                            accessibleName="Pagination"
                            onChange={this.handlePagination}
                            pageLimit={pagination.perPage}
                            start={pagination.start}
                            totalItems={pagination.totalResults}
                            itemsPerPageOptions={[
                                {
                                    label: '10',
                                    value: '10'
                                },
                                {
                                    label: '25',
                                    value: '25'
                                },
                                {
                                    label: '50',
                                    value: '50'
                                }
                            ]}
                        />

                    </div> : <div className="NoResults">No studies found.</div>}
                </div>}

                {representation === 'map' && <div className={classNames('Map', isLoadingMap && 'Loading')}>
                    <StudiesMap studies={map}/>
                </div>}
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
                                <Button icon="cross" className="CloseButton" onClick={this.toggleFilter}
                                        iconDescription="Close"/>
                            </div> : <div>
                                <Heading type="Subsection">Search studies</Heading>
                            </div>}

                            <div className="SideColItem">
                                <StudyFilters catalog={catalog} agent={agent}
                                              onFilter={(filter) => this.handleFilter(filter)}/>
                            </div>

                            {allowFilterOverlay && <div className="OverlayFooter">
                                <Button fullWidth onClick={this.toggleFilter}>View studies</Button>
                            </div>}
                        </div>
                    }}
                </Sticky>
            </div>
        </StickyContainer>;
    }
}