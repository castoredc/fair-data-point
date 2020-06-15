import React, {Component} from 'react'
import {Button} from "@castoredc/matter";
import './Modal.scss';
import {Portal} from "react-portal";

export default class Modal extends Component {
    handleClick = (event) => {
        const {handleClose} = this.props;

        if(event.target === event.currentTarget) {
            handleClose();
        }
    };

    render() {
        const {show, title, children, footer, handleClose, closeButton} = this.props;

        if(! show)
        {
            return null;
        }

        return <Portal>
            <div className="FullScreenOverlay" onClick={this.handleClick}>
                <div className="Modal">
                    {title && <header>{title}</header>}
                    {closeButton && <Button icon="cross" className="CloseButton" onClick={handleClose} iconDescription="Close" />}
                    <main>
                        {children}
                    </main>
                    {footer && <footer>
                        {footer}
                    </footer>}
                </div>
            </div>
        </Portal>
    }
}