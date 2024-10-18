import React, { FC } from 'react';
import './BackButton.scss';
import { LinkContainer } from 'react-router-bootstrap';
import { ArrowLeftIcon } from '@castoredc/matter-icons';
import { classNames } from '../../util';
import { useHistory } from 'react-router-dom';

export interface BackButtonProps {
    to?: string | (() => void);
    returnButton?: boolean;
    children?: React.ReactNode;
    sidebar?: boolean;
}

const BackButton: FC<BackButtonProps> = ({ to, returnButton, children, sidebar }) => {
    if (returnButton) {
        let history = useHistory();
        to = () => history.go(-1);
    }

    const button = (
        <button onClick={typeof to === 'function' ? to : undefined}>
            <span className="circle">
                <ArrowLeftIcon height="10px" width="10px" />
            </span>
            {children}
        </button>
    );

    return (
        <div className={classNames('BackButton', sidebar && 'Sidebar')}>
            {typeof to === 'string' ? <LinkContainer to={to}>{button}</LinkContainer> : button}
        </div>
    );
};

export default BackButton;
