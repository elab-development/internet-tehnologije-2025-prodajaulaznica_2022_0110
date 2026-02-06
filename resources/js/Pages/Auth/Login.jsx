import { useEffect } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset('password');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    return (
        <AuthLayout showBackButton={false}>
            <Head title="Prijava" />

            <h1 className="auth-title">Login</h1>

            {status && (
                <div className="mb-4 font-medium text-sm text-green-400">
                    {status}
                </div>
            )}

            <form onSubmit={submit} className="auth-form">
                {/* Email */}
                <div className="auth-input-group">
                    <label htmlFor="email" className="auth-label">Email</label>
                    <input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        required
                        className="auth-input"
                        autoComplete="username"
                        autoFocus
                    />
                    {errors.email && <div className="auth-error">{errors.email}</div>}
                </div>

                {/* Lozinka */}
                <div className="auth-input-group">
                    <label htmlFor="password" className="auth-label">Lozinka</label>
                    <input
                        id="password"
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        required
                        className="auth-input"
                        autoComplete="current-password"
                    />
                    {errors.password && <div className="auth-error">{errors.password}</div>}
                </div>

                {/* Remember me - opciono, možeš ukloniti ako ne želiš
                <div className="flex items-center" style={{ marginTop: '-0.5rem' }}>
                    <input
                        id="remember"
                        type="checkbox"
                        checked={data.remember}
                        onChange={(e) => setData('remember', e.target.checked)}
                        className="rounded border-gray-300 text-cyan-400 focus:ring-cyan-400"
                    />
                    <label htmlFor="remember" className="ml-2 text-sm" style={{ color: '#F8FAFC' }}>
                        Zapamti me
                    </label>
                </div>
                */}

                <button type="submit" disabled={processing} className="auth-button">
                    {processing ? 'Prijavljivanje...' : 'Login'}
                </button>
            </form>

            <div className="auth-footer">
                Nemate nalog?{' '}
                <Link href={route('register')} className="auth-footer-link">
                    Registrujte se
                </Link>
            </div>
        </AuthLayout>
    );
}
