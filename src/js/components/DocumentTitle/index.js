import {Component} from 'react';

class DocumentTitle extends Component {
    update = () => {
        document.title = this.props.title;
    };

    componentDidMount = this.update;
    componentDidUpdate = this.update;

    render() {
        return null;
    }
}

export default DocumentTitle;
