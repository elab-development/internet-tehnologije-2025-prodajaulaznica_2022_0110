import React from 'react';

function Button({
    children,
    onClick,
    type = 'button',
    disabled = false
}) {
    return (
        <button
            type={type}
            onClick={onClick}
            disabled={disabled}
            className="btn btn-primary"
        >
            {children}
        </button>
    );
}

export default Button;
