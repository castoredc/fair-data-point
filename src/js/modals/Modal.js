import React, {Component} from 'react'
import {Button, LoadingOverlay} from "@castoredc/matter";
import './Modal.scss';
import {Portal} from "react-portal";
import {classNames} from "../util";
import InlineLoader from "../components/LoadingScreen/InlineLoader";

export default class Modal extends Component {
    handleClick = (event) => {
        const {handleClose} = this.props;

        if(event.target === event.currentTarget) {
            handleClose();
        }
    };

    render() {
        const {show, title, children, footer, handleClose, className, closeButton, isLoading} = this.props;

        if(! show)
        {
            return null;
        }

        return <Portal>
            <div className="FullScreenOverlay" onClick={this.handleClick}>
                <div className={classNames('Modal', isLoading && 'Loading', className && className)}>
                    {title && <header>{title}</header>}
                    {closeButton && <Button icon="cross" className="CloseButton" onClick={handleClose} iconDescription="Close" />}
                    <main>
                        {isLoading && <LoadingOverlay accessibleLabel="Loading"/>}
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