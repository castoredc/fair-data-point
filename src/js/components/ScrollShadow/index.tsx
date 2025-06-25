import React, { useCallback, useEffect, useRef, useState } from 'react';
import { classNames, cloneIfComposite } from '../../util';
import EventListener from '../EventListener';
import './ScrollShadow.css';

type ScrollShadowProps = {
    children: React.ReactNode;
    className?: string;
};

const ScrollShadow: React.FC<ScrollShadowProps> = ({ children, className }) => {
    const scrollable = useRef<HTMLDivElement>(null);
    const [topShadow, setTopShadow] = useState(false);
    const [bottomShadow, setBottomShadow] = useState(false);

    const updateShadows = useCallback(() => {
        if (scrollable.current) {
            const { clientHeight, scrollHeight, scrollTop } = scrollable.current;
            setTopShadow(scrollTop > 0);
            setBottomShadow(scrollTop < scrollHeight - clientHeight);
        }
    }, []);

    useEffect(() => {
        updateShadows();
        // Also update shadows when window resizes
        window.addEventListener('resize', updateShadows);
        return () => {
            window.removeEventListener('resize', updateShadows);
        };
    }, [updateShadows]);

    return (
        <div
            className={classNames(className, 'ScrollShadow', topShadow && 'topShadow', bottomShadow && 'bottomShadow')}>
            <EventListener target={window} type="resize" listener={updateShadows} />
            <div className="scrollable" ref={scrollable} onScroll={updateShadows}>
                {cloneIfComposite(children, { onUpdate: updateShadows })}
            </div>
        </div>
    );
};

export default ScrollShadow;
