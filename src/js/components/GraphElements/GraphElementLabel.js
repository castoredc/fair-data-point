import React, {Component} from 'react'
import axios from 'axios'
import Icon from "../../components/Icon";
import {classNames} from "../../util";

class GraphElementLabel extends Component {
    constructor (props) {
        super(props);
        this.state = {
            isLoading: false,
            label: ''
        };
    }
    componentDidMount() {
        this.setState({isLoading: true});
        axios.get('/object',
            {
                params: {
                    iri: this.props.iri
                }
            })
            .then((response) => {
                this.setState({label: response.data.label, isLoading: false});
            })
            .catch((error) => {
                this.setState({isLoading: false});
            });
    }
    render() {
        const { iri, short, asLink, external } = this.props;

        const toGraphChildChild = (item, index) => {
            return <GraphChildChild key={index} {... item} />
        };

        if(asLink)
        {
            return <a className={classNames('GraphElementLabel', this.state.isLoading && 'Loading')} href={iri} target={external ? '_blank' : null}>
                {this.state.label !== '' ? this.state.label : short }
                {external && <span className="ExternalLink"><Icon type="newWindow" height={10} width={10}/></span>}
            </a>
        }

        return <div className={classNames('GraphElementLabel', this.state.isLoading && 'Loading')}>
            {this.state.label !== '' ? this.state.label : short }
        </div>
    }
}

export default GraphElementLabel
