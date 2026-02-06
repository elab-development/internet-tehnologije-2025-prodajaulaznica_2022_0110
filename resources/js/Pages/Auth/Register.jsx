import { useEffect } from 'react';
import AuthLayout from '@/Layouts/AuthLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import '../../../css/auth.css';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        surname: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: 'user',
    });

    useEffect(() => {
        return () => {
            reset('password', 'password_confirmation');
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route('register'));
    };

    return (
        <AuthLayout showBackButton={true}>
            <Head title="Registracija" />

            <h1 className="auth-title">Registracija</h1>

            <form onSubmit={submit} className="auth-form">
                {/* Ime */}
                <div className="auth-input-group">
                    <label htmlFor="name" className="auth-label">Ime</label>
                    <input
                        id="name"
                        type="text"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        required
                        className="auth-input"
                        autoComplete="given-name"
                    />
                    {errors.name && <div className="auth-error">{errors.name}</div>}
                </div>

                {/* Prezime */}
                <div className="auth-input-group">
                    <label htmlFor="surname" className="auth-label">Prezime</label>
                    <input
                        id="surname"
                        type="text"
                        value={data.surname}
                        onChange={(e) => setData('surname', e.target.value)}
                        required
                        className="auth-input"
                        autoComplete="family-name"
                    />
                    {errors.surname && <div className="auth-error">{errors.surname}</div>}
                </div>

                {/* Korisničko ime */}
                <div className="auth-input-group">
                    <label htmlFor="username" className="auth-label">Korisničko ime</label>
                    <input
                        id="username"
                        type="text"
                        value={data.username}
                        onChange={(e) => setData('username', e.target.value)}
                        required
                        className="auth-input"
                        autoComplete="username"
                    />
                    {errors.username && <div className="auth-error">{errors.username}</div>}
                </div>

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
                        autoComplete="email"
                    />
                    {errors.email && <div className="auth-error">{errors.email}</div>}
                </div>

                {/* Uloga */}
                <div className="auth-input-group">
                    <label htmlFor="role" className="auth-label">Pozicija</label>
                    <select
                        id="role"
                        value={data.role}
                        onChange={(e) => setData('role', e.target.value)}
                        required
                        className="auth-select"
                    >
                        <option value="user">Korisnik</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Administrator</option>
                    </select>
                    {errors.role && <div className="auth-error">{errors.role}</div>}
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
                        autoComplete="new-password"
                    />
                    {errors.password && <div className="auth-error">{errors.password}</div>}
                </div>

                {/* Potvrda lozinke */}
                <div className="auth-input-group">
                    <label htmlFor="password_confirmation" className="auth-label">Potvrdite lozinku</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        required
                        className="auth-input"
                        autoComplete="new-password"
                    />
                    {errors.password_confirmation && <div className="auth-error">{errors.password_confirmation}</div>}
                </div>

                <button type="submit" disabled={processing} className="auth-button">
                    {processing ? 'Registrovanje...' : 'Registruj se'}
                </button>
            </form>
        </AuthLayout>
    );
}
