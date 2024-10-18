import React from 'react';

interface EmojiProps {
    label?: string;
    symbol: string;
}

const Emoji: React.FC<EmojiProps> = ({ label, symbol }) => (
    <span
        className="emoji"
        role="img"
        aria-label={label ? label : ''}
        aria-hidden={label ? 'false' : 'true'}
    >
        {symbol}
    </span>
);

export default Emoji;