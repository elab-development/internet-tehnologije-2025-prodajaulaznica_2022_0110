import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import axios from 'axios';
import Input from './Input';
import Button from './Button';

function Register() {
    const navigate = useNavigate();
    const [formData, setFormData] = useState({
        name: '',
        surname: '',
        username: '',
        email: '',
        password: '',
        password_confirmation: '',
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
        if (errors[e.target.name]) {
            setErrors({
                ...errors,
                [e.target.name]: ''
            });
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            const response = await axios.post('/api/register', formData);

            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));

            navigate('/');
        } catch (error) {
            if (error.response?.data?.errors) {
                setErrors(error.response.data.errors);
            } else {
                setErrors({ general: 'Došlo je do greške. Pokušajte ponovo.' });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="auth-container">
            <div className="auth-box">
                <div className="auth-header">
                    <Link to="/" className="back-link">
                        ← Nazad
                    </Link>
                    <h1 className="auth-title">Registracija</h1>
                </div>

                <div className="auth-card">
                    <form onSubmit={handleSubmit}>
                        <Input
                            label="Ime"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            placeholder="Unesite ime"
                            required
                            error={errors.name?.[0]}
                        />

                        <Input
                            label="Prezime"
                            name="surname"
                            value={formData.surname}
                            onChange={handleChange}
                            placeholder="Unesite prezime"
                            required
                            error={errors.surname?.[0]}
                        />

                        <Input
                            label="Korisničko ime"
                            name="username"
                            value={formData.username}
                            onChange={handleChange}
                            placeholder="Unesite korisničko ime"
                            required
                            error={errors.username?.[0]}
                        />

                        <Input
                            label="Email"
                            type="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            placeholder="Unesite email"
                            required
                            error={errors.email?.[0]}
                        />

                        <Input
                            label="Lozinka"
                            type="password"
                            name="password"
                            value={formData.password}
                            onChange={handleChange}
                            placeholder="Unesite lozinku"
                            required
                            error={errors.password?.[0]}
                        />

                        <Input
                            label="Potvrdite lozinku"
                            type="password"
                            name="password_confirmation"
                            value={formData.password_confirmation}
                            onChange={handleChange}
                            placeholder="Potvrdite lozinku"
                            required
                        />

                        {errors.general && (
                            <div className="error-alert">
                                {errors.general}
                            </div>
                        )}

                        <Button type="submit" disabled={loading}>
                            {loading ? 'Registrovanje...' : 'Registruj se'}
                        </Button>
                    </form>

                    <div className="auth-footer">
                        Već imate nalog?{' '}
                        <Link to="/login">Prijavite se</Link>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Register;
