import React, {Component} from "react";
import axios from "axios";
import {Col, Row} from "react-bootstrap";
import InlineLoader from "../LoadingScreen/InlineLoader";
import {toast} from "react-toastify";
import ToastContent from "../ToastContent";
import {DataTable} from "@castoredc/matter";
import {classNames, localizedText} from "../../util";

export default class DistributionsDataTable extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingDistributions: true,
            hasLoadedDistributions: false,
            distributions:          [],
        };

        this.tableRef = React.createRef();
    }

    componentDidMount() {
        this.getDistributions();
    }

    getDistributions = () => {
        const { pagination, hasLoadedDistributions } = this.state;
        const { dataset } = this.props;

        this.setState({
            isLoadingDistributions: true,
        });

        if(hasLoadedDistributions) {
            window.scrollTo(0, this.tableRef.current.offsetTop - 35);
        }

        axios.get(dataset ? '/api/dataset/' + dataset.slug + '/distribution' : '/api/distribution')
            .then((response) => {
                this.setState({
                    distributions:          response.data,
                    isLoadingDistributions: false,
                    hasLoadedDistributions: true,
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistributions: false,
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distributions';
                toast.error(<ToastContent type="error" message={message}/>);
            });
    };

    render() {
        const { distributions, isLoadingDistributions, hasLoadedDistributions } = this.state;
        const { history, catalog, dataset } = this.props;

        if(!hasLoadedDistributions) {
            return <Row>
                <Col>
                    <InlineLoader />
                </Col>
            </Row>;
        }

        return <div className="FillHeight">
        <div className="Page">
            <div className={classNames('SelectableDataTable FullHeightDataTable', isLoadingDistributions && 'Loading')} ref={this.tableRef}>
                <div className="DataTableWrapper">
                    <DataTable
                        emptyTableMessage="No distributions found"
                        highlightRowOnHover
                        cellSpacing="default"
                        onClick={(event, rowID, index) => {
                            if(typeof index !== "undefined") {
                                history.push('/admin' + (dataset ? '/dataset/' + dataset.slug : '') + '/distribution/' + distributions[index].slug)
                            }
                        }}
                        rows={distributions.map((item) => {
                            return [
                                item.hasMetadata ? localizedText(item.metadata.title, 'en') : '(no title)',
                                item.hasMetadata ? localizedText(item.metadata.description, 'en') : '',
                                item.type ? item.type.toUpperCase() : '',
                                item.hasMetadata ? item.metadata.language : '',
                                item.hasMetadata ? item.metadata.license : '',
                                item.published ? {
                                    type: 'view'
                                } : undefined,
                            ];
                        })}
                        structure={{
                            title: {
                                header:    'Title',
                                resizable: true,
                                template:  'text',
                            },
                            description: {
                                header:    'Description',
                                resizable: true,
                                template:  'text',
                            },
                            type: {
                                header:    'Type',
                                resizable: true,
                                template:  'text',
                            },
                            language: {
                                header:    'Language',
                                resizable: true,
                                template:  'text',
                            },
                            license: {
                                header:    'License',
                                resizable: true,
                                template:  'text',
                            },
                            published: {
                                header:    'Published',
                                icon:      'view',
                                template:  'icon'
                            },
                        }}
                    />
                </div>
            </div>
        </div>
    </div>;
    }
}