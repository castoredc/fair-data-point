import React, { Component } from "react";
import axios from "axios/index";

import GraphElements from '../../components/GraphElements'
import {Container, Button} from "react-bootstrap";

import './RDFRender.scss';
import LoadingScreen from "../../components/LoadingScreen";
import Logo from "../../components/Logo";
import DocumentTitle from "../../components/DocumentTitle";
import Editor from "../../components/Editor";

export default class RDFRender extends Component {
    constructor(props) {
        super(props);
        this.state = {
            isLoading: true,
            isLoaded: false,
            hasError: false,
            errorMessage: '',
            graph: [],
            label: '',
            turtle: '',
            showTurtle: false
        };
    }

    componentDidMount() {
        axios.get(window.location.href + '?json')
            .then((response) => {
                this.setState({
                    graph: response.data.graph,
                    label: response.data.label,
                    turtle: response.data.turtle,
                    isLoading: false,
                    isLoaded: true
                });
            })
            .catch((error) => {
                if(error.response && typeof error.response.data.message !== "undefined")
                {
                    this.setState({
                        isLoading: false,
                        hasError: true,
                        errorMessage: error.response.data.message
                    });
                } else {
                    this.setState({
                        isLoading: false
                    });
                }
            });
    }

    toggleTurtle = () => {
        var showTurtle = !this.state.showTurtle;
        this.setState({
            showTurtle: showTurtle
        });
    };

    render() {
        return (
            <Container className="RDFRenderContainer">
                {this.state.isLoaded ?
                    <div className="RDFRender">
                        <DocumentTitle title={this.state.label} />
                        <div className="RDFRenderHeader">
                            <Logo />
                            <Button size="sm" className="TurtleButton" onClick={this.toggleTurtle} variant="link">
                                Raw data
                            </Button>
                        </div>
                        <div className="RDFRenderBody">
                            <h1>{this.state.label}</h1>

                            <GraphElements graph={this.state.graph} />
                        </div>
                        <div className="RDFRenderFooter">
                        </div>

                        <Editor show={this.state.showTurtle} title={this.state.label} turtle={this.state.turtle} toggleFunction={this.toggleTurtle} />

                    </div>
                    : <LoadingScreen showLoading={true}/>
                }
            </Container>
        );
    }
}
