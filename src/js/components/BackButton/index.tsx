import React, {FC} from 'react';
import './BackButton.scss';
import {LinkContainer} from "react-router-bootstrap";
import {ArrowLeftIcon} from "@castoredc/matter-icons";
import {classNames} from "../../util";
import {useHistory} from "react-router-dom";

interface BackButtonProps {
    to?: string,
    returnButton?: boolean,
    children: React.ReactNode,
    sidebar?: boolean,
}

const BackButton: FC<BackButtonProps> = ({to, returnButton, children, sidebar}) => {
    if (returnButton) {
        let history = useHistory();

        return <div className={classNames('BackButton', sidebar && 'Sidebar')}>
            <button onClick={() => history.go(-1)}>
                <span className="circle">
                    <ArrowLeftIcon height="10px" width="10px"/>
                </span>

                {children}
            </button>
        </div>;
    }

    return <div className={classNames('BackButton', sidebar && 'Sidebar')}>
        <LinkContainer to={to}>
            <button>
                <span className="circle">
                    <ArrowLeftIcon height="10px" width="10px"/>
                </span>

                {children}
            </button>
        </LinkContainer>
    </div>;
}

export default BackButton;