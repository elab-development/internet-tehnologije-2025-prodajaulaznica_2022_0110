import AuthLayout from '@/Layouts/AuthLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import '../../../css/auth.css';

export default function CreateEvent({ categories }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        location: '',
        date_start: '',
        date_end: '',
        category_id: '',
        status: 'published',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('events.store'));
    };

    return (
        <AuthLayout showBackButton={true}>
            <Head title="Kreiraj događaj" />

            <h1 className="auth-title">Kreiraj novi događaj</h1>

            <form onSubmit={submit} className="auth-form">
                <div className="auth-input-group">
                    <label htmlFor="title" className="auth-label">Naziv događaja</label>
                    <input
                        id="title"
                        type="text"
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                        required
                        className="auth-input"
                    />
                    {errors.title && <div className="auth-error">{errors.title}</div>}
                </div>

                <div className="auth-input-group">
                    <label htmlFor="description" className="auth-label">Opis</label>
                    <textarea
                        id="description"
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                        required
                        rows="4"
                        className="auth-input"
                    />
                    {errors.description && <div className="auth-error">{errors.description}</div>}
                </div>

                <div className="auth-input-group">
                    <label htmlFor="location" className="auth-label">Lokacija</label>
                    <input
                        id="location"
                        type="text"
                        value={data.location}
                        onChange={(e) => setData('location', e.target.value)}
                        required
                        className="auth-input"
                    />
                    {errors.location && <div className="auth-error">{errors.location}</div>}
                </div>

                <div className="auth-input-group">
                    <label htmlFor="category_id" className="auth-label">Kategorija</label>
                    <select
                        id="category_id"
                        value={data.category_id}
                        onChange={(e) => setData('category_id', e.target.value)}
                        required
                        className="auth-select"
                    >
                        <option value="">Izaberite kategoriju</option>
                        {categories.map((cat) => (
                            <option key={cat.id} value={cat.id}>
                                {cat.name}
                            </option>
                        ))}
                    </select>
                    {errors.category_id && <div className="auth-error">{errors.category_id}</div>}
                </div>

                <div className="auth-input-group">
                    <label htmlFor="date_start" className="auth-label">Datum i vreme početka</label>
                    <input
                        id="date_start"
                        type="datetime-local"
                        value={data.date_start}
                        onChange={(e) => setData('date_start', e.target.value)}
                        required
                        className="auth-input"
                    />
                    {errors.date_start && <div className="auth-error">{errors.date_start}</div>}
                </div>

                <div className="auth-input-group">
                    <label htmlFor="date_end" className="auth-label">Datum i vreme kraja</label>
                    <input
                        id="date_end"
                        type="datetime-local"
                        value={data.date_end}
                        onChange={(e) => setData('date_end', e.target.value)}
                        required
                        className="auth-input"
                    />
                    {errors.date_end && <div className="auth-error">{errors.date_end}</div>}
                </div>

                <div className="auth-input-group">
                    <label htmlFor="status" className="auth-label">Status</label>
                    <select
                        id="status"
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        required
                        className="auth-select"
                    >
                        <option value="draft">Draft</option>
                        <option value="published">Objavljen</option>
                        <option value="cancelled">Otkazan</option>
                    </select>
                    {errors.status && <div className="auth-error">{errors.status}</div>}
                </div>

                <div className="flex gap-4 mt-6">
                    <button
                        type="submit"
                        disabled={processing}
                        className="flex-1 text-center px-4 py-3 bg-cyan-400 text-gray-900 rounded-full font-bold hover:bg-purple-600 hover:text-white transition"
                    >
                        {processing ? 'Kreiranje...' : 'Kreiraj događaj'}
                    </button>
                    <Link
                        href={route('dashboard')}
                        className="flex-1 text-center px-4 py-3 bg-slate-600 text-white rounded-full font-bold hover:bg-slate-500 transition"
                    >
                        Otkaži
                    </Link>
                </div>
            </form>
        </AuthLayout>
    );
}
