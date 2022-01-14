import React, {useCallback, useEffect, useRef, useState} from 'react';
import {classNames, cloneIfComposite} from '../../util';
import EventListener from '../EventListener';
import './ScrollShadow.scss';

const ScrollShadow = ({children, className}) => {
    const scrollable = useRef(null);
    const [topShadow, setTopShadow] = useState(false);
    const [bottomShadow, setBottomShadow] = useState(false);

    const updateShadows = useCallback(() => {
        if (!scrollable.current) return;
        const {clientHeight, scrollHeight, scrollTop} = scrollable.current;
        setTopShadow(scrollTop > 0);
        setBottomShadow(scrollTop < scrollHeight - clientHeight);
    }, []);

    useEffect(updateShadows, []);

    return (
        <div
            className={classNames(
                className,
                'ScrollShadow',
                topShadow && 'topShadow',
                bottomShadow && 'bottomShadow'
            )}
        >
            <EventListener target={window} type="resize" listener={updateShadows}/>
            <div className="scrollable" ref={scrollable} onScroll={updateShadows}>
                {cloneIfComposite(children, {onUpdate: updateShadows})}
            </div>
        </div>
    );
};

export default ScrollShadow;
