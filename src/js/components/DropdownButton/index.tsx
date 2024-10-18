import React, { useState, useRef, useEffect } from 'react';
import { Button, ButtonVariants, Icon, Menu } from '@castoredc/matter';
import './DropdownButton.scss';
import { classNames } from '../../util';
import EventListener from '../EventListener';
import { MatterIcon } from '@castoredc/matter-icons';

interface DropdownButtonProps {
    className?: string;
    icon?: MatterIcon;
    iconDescription?: string;
    text?: string;
    buttonType?: ButtonVariants;
    hideDropdown?: boolean;
    items: any[]; // Adjust type based on Menu items type
}

const DropdownButton: React.FC<DropdownButtonProps> = ({
                                                           className,
                                                           icon,
                                                           iconDescription,
                                                           text,
                                                           buttonType = 'contentOnly',
                                                           hideDropdown = false,
                                                           items,
                                                       }) => {
    const [isOpen, setIsOpen] = useState(false);

    const menuRef = useRef<HTMLUListElement | null>(null);
    const contentRef = useRef<HTMLDivElement | null>(null);

    const setMenuStatus = (status: boolean) => {
        setIsOpen(status);
    };

    const closeMenu = () => {
        setIsOpen(false);
    };

    const closeMenuOnOutsideClick = (event: MouseEvent) => {
        const menuElement = menuRef.current;
        if (!menuElement) return;

        const targetElement = event.target as Node;
        const isClickOutsideMenu = !menuElement.contains(targetElement);
        if (!isClickOutsideMenu) return;

        // Moves the focus back to the trigger element and closes the menu
        closeMenu();
    };

    useEffect(() => {
        if (isOpen) {
            document.addEventListener('click', closeMenuOnOutsideClick);
        } else {
            document.removeEventListener('click', closeMenuOnOutsideClick);
        }

        return () => {
            document.removeEventListener('click', closeMenuOnOutsideClick);
        };
    }, [isOpen]);

    return (
        <div className={classNames('DropdownButtonContainer', className)}>
            <Button
                className={classNames('DropdownButton', isOpen && 'Open')}
                iconDescription={iconDescription}
                onClick={() => setMenuStatus(!isOpen)}
                aria-expanded={hideDropdown ? undefined : isOpen}
                buttonType={buttonType}
                isDropdown={!hideDropdown}
                isOpen={hideDropdown ? undefined : isOpen}
            >
                {icon && <Icon type={icon} />}
                {text && text}
            </Button>

            {isOpen && (
                <div className="DropdownButtonMenuWrapper" ref={contentRef}>
                    <Menu forwardRef={menuRef} trapFocus onEscape={closeMenu} items={items} />
                </div>
            )}
        </div>
    );
};

export default DropdownButton;