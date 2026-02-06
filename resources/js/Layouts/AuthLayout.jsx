import { Link } from '@inertiajs/react';

export default function AuthLayout({ children, showBackButton = false }) {
    return (
        <div className="auth-page">
            {/* Header */}
            <div className="auth-header-bar">
                <div className="auth-header-title">EPA Prodaja Karata</div>
                {showBackButton && (
                    <Link href="/" className="auth-back-link">
                        ← Nazad
                    </Link>
                )}
            </div>

            {/* Main content */}
            <div className="auth-container">
                <div className="auth-box">
                    {children}
                </div>
            </div>
        </div>
    );
}
