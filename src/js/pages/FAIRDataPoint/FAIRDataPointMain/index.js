import React, {Component} from "react";
import axios from "axios/index";
import LoadingScreen from "../../../components/LoadingScreen";
import {localizedText} from "../../../util";
import ListItem from "../../../components/ListItem";
import FAIRDataInformation from "../../../components/FAIRDataInformation";
import queryString from "query-string";
import {toast} from "react-toastify";
import ToastContent from "../../../components/ToastContent";

export default class FAIRDataPointMain extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoadingFDP: true,
            isLoadingCatalogs: true,
            hasLoadedFDP: false,
            hasLoadedCatalogs: false,
            fdp: null,
            catalogs: []
        };
    }

    componentDidMount() {
        this.getFDP();
        this.getCatalogs();
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

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the FAIR Data Point information';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getCatalogs = () => {
        axios.get('/api/catalog')
            .then((response) => {
                this.setState({
                    catalogs: response.data,
                    isLoadingCatalogs: false,
                    hasLoadedCatalogs: true
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingCatalogs: false
                });

                const message = (error.response && typeof error.response.data.message !== "undefined") ? error.response.data.message : 'An error occurred while loading the catalogs';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    render() {
        const params = queryString.parse(this.props.location.search);
        const embedded = (typeof params.embed !== 'undefined');

        if(this.state.isLoadingFDP || this.state.isLoadingCatalogs)
        {
            return <LoadingScreen showLoading={true}/>;
        }

        return <FAIRDataInformation
            embedded={embedded}
            className="FAIRDataPoint"
            title={localizedText(this.state.fdp.title, 'en')}
            version={this.state.fdp.version}
            license={this.state.fdp.license}
        >
            <h2>Catalogs</h2>
            <div className="Description">
                Catalogs are collections of datasets.
            </div>
            {this.state.catalogs.length > 0 ? this.state.catalogs.map((item, index) => {
                return <ListItem key={index}
                                 newWindow={embedded}
                                 link={item.relative_url}
                                 title={localizedText(item.title, 'en')}
                                 description={localizedText(item.description, 'en')} />}
            ) : <div className="NoResults">No catalogs found.</div>}
        </FAIRDataInformation>;

        // {this.state.fdp.publishers.length > 0 && <div className="Publishers">
        //     {this.state.fdp.publishers.map((item, index) => {
        //         return <Contact key={index}
        //                         url={item.url}
        //                         type={item.type}
        //                         name={item.name} />}
        //     )}
        // </div>}
    }
}
