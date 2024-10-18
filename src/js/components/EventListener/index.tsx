import React, { useEffect } from 'react';

interface EventListenerProps {
    target: EventTarget;
    type: string;
    listener: EventListenerOrEventListenerObject;
}

const EventListener: React.FC<EventListenerProps> = ({ target, type, listener }) => {
    useEffect(() => {
        // Add event listener on mount
        target.addEventListener(type, listener);

        // Remove event listener on unmount
        return () => {
            target.removeEventListener(type, listener);
        };
    }, [target, type, listener]); // Re-run effect if target, type, or listener changes

    return null;
};

export default EventListener;