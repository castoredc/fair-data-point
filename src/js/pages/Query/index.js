import React, {Component} from "react";
import FAIRDataInformation from "../../components/FAIRDataInformation";
import axios from "axios";
import {toast} from "react-toastify";
import ToastContent from "../../components/ToastContent";
import {localizedText} from "../../util";
import LoadingScreen from "../../components/LoadingScreen";
import Yasgui from "@triply/yasgui";
import "@triply/yasgui/build/yasgui.min.css";
import './Query.scss';

export default class Query extends Component {
    constructor(props) {
        super(props);

        this.state = {
            hasDistribution:        !! props.match.params.distribution,
            isLoadingDistribution:  false,
            hasLoadedDistribution:  false,
            distribution:           null,
            prefixes:               []
        };
    }

    componentDidMount() {
        const { match } = this.props;

        if(match.params.distribution) {
            this.getDistribution();
        } else {
            this.createYasgui();
        }
    }

    getDistribution = () => {
        const { match } = this.props;

        axios.get('/api/dataset/' + match.params.dataset + '/distribution/' + match.params.distribution)
            .then((response) => {
                this.setState({
                    distribution: response.data,
                    isLoadingDistribution: false,
                    hasLoadedDistribution: true
                }, () => {
                    this.getPrefixes()
                });
            })
            .catch((error) => {
                this.setState({
                    isLoadingDistribution: false
                });

                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the distribution';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    getPrefixes = () => {
        const { distribution } = this.state;

        axios.get('/api/model/' + distribution.dataModel + '/prefix')
            .then((response) => {
                this.setState({
                    prefixes: response.data
                }, () => {
                    this.createYasgui()
                });
            })
            .catch((error) => {
                const message = (error.response && typeof error.response.data.error !== "undefined") ? error.response.data.error : 'An error occurred while loading the prefixes';
                toast.error(<ToastContent type="error" message={message} />);
            });
    };

    createYasgui = () => {
        const { distribution, prefixes } = this.state;

        let configPrefixes = prefixes.reduce(function(map, obj) {
            map[obj.prefix] = obj.uri;
            return map;
        }, {});

        let config = {};

        Yasgui.Yasr.defaults.persistencyExpire = 0;
        Yasgui.Yasr.defaults.prefixes = configPrefixes;
 
        if(distribution) {
            config['requestConfig'] = {
                endpoint: window.location.origin + distribution.relativeUrl + '/sparql'
            }
        }

        this.yasgui = new Yasgui(document.getElementById("yasgui"), config);
    };

    render() {
        const { hasDistribution, hasLoadedDistribution, distribution } = this.state;
        const { location } = this.props;

        let title = 'Query';

        if(hasDistribution && hasLoadedDistribution) {
            title = `Query ${localizedText(distribution.metadata.title, 'en')}`
        }

        return <FAIRDataInformation
            className="Query"
            title={title}
            breadcrumbs ={{...location.state, query: true}}
        >
            {!hasLoadedDistribution && <LoadingScreen showLoading={true} />}
            <div id="yasgui" />
        </FAIRDataInformation>;
    }
}
