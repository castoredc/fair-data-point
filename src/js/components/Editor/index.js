import React, {Component} from 'react'
import YASQE from "yasgui-yasqe";
import "yasgui-yasqe/dist/yasqe.css";
import './Editor.scss'
import {classNames} from "../../util";
import Icon from "../../components/Icon";
import {Button} from "react-bootstrap";

class Editor extends Component {
    constructor (props) {
        super(props);
        this.state = {
            isLoading: false
        };
    }
    componentDidUpdate() {
        this.yasqe.refresh();
    }
    componentDidMount() {
        YASQE.defaults.syntaxErrorCheck = false;
        YASQE.defaults.createShareLink = null;

        this.yasqe = YASQE.fromTextArea(document.getElementById("yasqe"), {
        });
    }

    render() {
        const { title, turtle, show, toggleFunction } = this.props;

        return <div className={classNames('Editor', show && 'Active')}>
            <div className="EditorHeader">
                <h1>{title}</h1>
                <Button className="CloseButton" variant="link" onClick={toggleFunction}>
                    <Icon type="crossThick" height={14} width={14} />
                </Button>
            </div>
            <textarea id="yasqe" value={turtle} readOnly/>
        </div>
    }
}

export default Editor
