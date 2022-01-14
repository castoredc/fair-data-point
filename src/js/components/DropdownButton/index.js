import React, {Component, createRef} from 'react';

import {Button, Menu} from "@castoredc/matter";
import './DropdownButton.scss';
import {classNames} from "../../util";
import EventListener from "../EventListener";

class DropdownButton extends Component {
    constructor(props) {
        super(props);

        this.state = {
            isOpen: false,
        };


        this.triggerRef = createRef();
        this.menuRef = createRef();
        this.contentRef = createRef();
    }

    setMenuStatus = (status) => {
        this.setState({
            isOpen: status,
        });
    }

    closeMenu = () => {
        this.setState({
            isOpen: false,
        });
    }

    closeMenuOnOutsideClick = (event) => {
        const {current: menuElement} = this.menuRef;
        if (!menuElement) return;

        const targetElement = event.target;
        const isClickOutsideMenu = !menuElement.contains(targetElement);
        if (!isClickOutsideMenu) return;

        // moves the focus back to the trigger element and closes the menu
        this.closeMenu();
    };

    render() {
        const {isOpen} = this.state;
        const {
            className,
            icon,
            iconDescription,
            text,
            buttonType = 'contentOnly',
            hideDropdown = false,
            items
        } = this.props;

        return <div className={classNames('DropdownButtonContainer', className)}>
            <Button
                className={classNames('DropdownButton', isOpen && 'Open')}
                icon={icon}
                iconDescription={iconDescription}
                onClick={() => this.setMenuStatus(!isOpen)}
                aria-expanded={hideDropdown ? undefined : isOpen}
                buttonType={buttonType}
                isDropdown={!hideDropdown}
                isOpen={hideDropdown ? undefined : isOpen}
            >
                {text}
            </Button>

            {isOpen && (
                <div className="DropdownButtonMenuWrapper" ref={this.contentRef}>
                    <EventListener target={document} type="click" listener={this.closeMenuOnOutsideClick}/>
                    <Menu forwardRef={this.menuRef} trapFocus onEscape={this.closeMenu} items={items}/>
                </div>
            )}
        </div>
    }
}

export default DropdownButton;
