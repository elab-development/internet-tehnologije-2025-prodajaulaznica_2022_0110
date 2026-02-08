import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';

export default function Dashboard({ auth, events, categories, filters }) {
    console.log('Props:', usePage().props);
    console.log('Success message:', usePage().props.success);
    const [search, setSearch] = useState(filters.search || '');
    const [category, setCategory] = useState(filters.category || '');
    const [dateFrom, setDateFrom] = useState(filters.date_from || '');
    const [dateTo, setDateTo] = useState(filters.date_to || '');

    const handleFilter = () => {
        router.get('/dashboard', {
            search: search,
            category: category,
            date_from: dateFrom,
            date_to: dateTo,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleReset = () => {
        setSearch('');
        setCategory('');
        setDateFrom('');
        setDateTo('');
        router.get('/dashboard');
    };

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('sr-RS', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        });
    };

    const formatTime = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleTimeString('sr-RS', {
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const isAdmin = auth.user?.role === 'admin';
    const isModerator = auth.user?.role === 'moderator';
    const isUser = auth.user?.role === 'user';

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Dashboard" />
            {usePage().props.flash?.success && (
                <div className="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-2xl z-50 animate-fade-in-out">
                    ✅ {usePage().props.flash.success}
                </div>
            )}
            <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 py-8">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Header Section */}
                    <div className="mb-8 flex justify-between items-center">
                        <div>
                            <h1 className="text-4xl font-bold text-cyan-400 mb-2">
                                Dobrodošli, {auth.user.name}!
                            </h1>
                            <p className="text-gray-400">
                                {isAdmin && 'Administrator panel'}
                                {isModerator && '⚙️ Moderator panel'}
                                {isUser && 'Pregledajte aktuelne događaje i rezervišite ulaznice'}
                            </p>
                        </div>
                        {(isAdmin || isModerator) && (
                            <Link
                                href="/events/create"
                                className="px-6 py-3 bg-cyan-400 text-gray-900 rounded-lg font-bold hover:shadow-xl hover:scale-105 transition-all duration-300"
                            >
                                + Kreiraj novi događaj
                            </Link>
                        )}
                    </div>

                    {/* Search & Filters */}
                    <div className="bg-slate-800 rounded-2xl p-6 mb-8 shadow-2xl border border-slate-700">
                        <h3 className="text-xl font-semibold text-cyan-400 mb-4 flex items-center gap-2">
                            🔍 Pretraga događaja
                        </h3>
                        <div className="grid grid-cols-1 md:grid-cols-6 gap-4">
                            {/* Search */}
                            <input
                                type="text"
                                placeholder="Pretraži po nazivu..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyPress={(e) => e.key === 'Enter' && handleFilter()}
                                className="bg-slate-700 border-2 border-slate-600 focus:border-cyan-400 md:col-span-2 px-4 py-3 rounded-lg text-white placeholder-gray-500 focus:outline-none"
                            />

                            {/* Category */}
                            <select
                                value={category}
                                onChange={(e) => setCategory(e.target.value)}
                                className="bg-slate-700 border-2 border-slate-600 focus:border-cyan-400 px-4 py-3 rounded-lg text-white focus:outline-none"
                            >
                                <option value="">Sve kategorije</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>

                            {/* Date From */}
                            <input
                                type="date"
                                value={dateFrom}
                                onChange={(e) => setDateFrom(e.target.value)}
                                placeholder="Od datuma"
                                className="bg-slate-700 border-2 border-slate-600 focus:border-cyan-400 px-4 py-3 rounded-lg text-white focus:outline-none"
                            />

                            {/* Date To */}
                            <input
                                type="date"
                                value={dateTo}
                                onChange={(e) => setDateTo(e.target.value)}
                                placeholder="Do datuma"
                                className="bg-slate-700 border-2 border-slate-600 focus:border-cyan-400 px-4 py-3 rounded-lg text-white focus:outline-none"
                            />

                            {/* Buttons */}
                            <div className="flex gap-2">
                                <button
                                    onClick={handleFilter}
                                    className="flex-1 px-4 py-3 bg-cyan-400 text-gray-900 rounded-lg font-bold hover:bg-cyan-500 transition-all duration-200"
                                >
                                    Pretraži
                                </button>
                                <button
                                    onClick={handleReset}
                                    className="px-4 py-3 bg-slate-600 text-white rounded-lg hover:bg-slate-500 transition-all duration-200"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Events Count */}
                    <div className="mb-6">
                        <p className="text-gray-400">
                            Pronađeno <strong className="text-cyan-400 text-xl">{events.total}</strong> događaja
                        </p>
                    </div>

                    {/* Events Grid 3x3 */}
                    {events.data.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {events.data.map((event) => (
                                <div
                                    key={event.id}
                                    className="bg-slate-700 border-2 border-slate-600 hover:border-cyan-400 hover:shadow-2xl hover:shadow-cyan-400/20 hover:-translate-y-1 rounded-2xl overflow-hidden transition-all duration-300"
                                >
                                    <div className="p-6">
                                        {/* Category Badge */}
                                        <div className="mb-3">
                                            <span className="inline-block px-4 py-1 text-xs font-bold rounded-full bg-cyan-400 text-gray-900">
                                                {event.category.name}
                                            </span>
                                        </div>

                                        {/* Title */}
                                        <h3 className="text-2xl font-bold text-white mb-3 line-clamp-2">
                                            {event.title}
                                        </h3>

                                        {/* Description */}
                                        <p className="text-gray-400 text-sm mb-4 line-clamp-2">
                                            {event.description}
                                        </p>

                                        {/* Event Details */}
                                        <div className="text-sm text-gray-400 space-y-2 mb-4">
                                            <div className="flex items-center gap-2">
                                                <span className="text-lg">📍</span>
                                                <span className="line-clamp-1">{event.location}</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-lg">📅</span>
                                                <span>{formatDate(event.date_start)} - {formatDate(event.date_end)}</span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <span className="text-lg">🕐</span>
                                                <span>{formatTime(event.date_start)}</span>
                                            </div>
                                        </div>

                                        {/* Price Info */}
                                        {event.ticket_types && event.ticket_types.length > 0 && (
                                            <div className="mb-4 p-4 bg-slate-700 rounded-lg border border-slate-600">
                                                <p className="text-xs text-gray-400 mb-1">
                                                    Cena od:
                                                </p>
                                                <p className="text-2xl font-bold text-cyan-400">
                                                    {Math.min(...event.ticket_types.map(t => t.price)).toLocaleString()} RSD
                                                </p>
                                            </div>
                                        )}

                                        {/* Action Buttons */}
                                        <div className="flex gap-2">
                                            <Link
                                                href={`/events/${event.id}`}
                                                className="flex-1 text-center px-4 py-3 bg-cyan-400 text-gray-900 rounded-lg font-bold hover:bg-cyan-500 hover:shadow-xl hover:scale-105 transition-all duration-200"
                                            >
                                                Pogledaj ponudu
                                            </Link>

                                            {isAdmin && (
                                                <>
                                                    <Link
                                                        href={`/events/${event.id}/edit`}
                                                        className="px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-all duration-200"
                                                        title="Izmeni"
                                                    >
                                                        Izmeni
                                                    </Link>
                                                    <button
                                                        onClick={() => {
                                                            if (confirm('Da li ste sigurni da želite da obrišete ovaj događaj?')) {
                                                                router.delete(`/events/${event.id}`);
                                                            }
                                                        }}
                                                        className="px-4 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200"
                                                        title="Obriši"
                                                    >
                                                        Briši
                                                    </button>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-slate-800 rounded-2xl shadow-xl border border-slate-700">
                            <div className="text-6xl mb-4">😕</div>
                            <p className="text-gray-400 text-xl mb-6">
                                Nema pronađenih događaja
                            </p>
                            <button
                                onClick={handleReset}
                                className="px-8 py-4 bg-cyan-400 text-gray-900 rounded-lg font-bold hover:bg-cyan-500 transition"
                            >
                                Resetuj filtere
                            </button>
                        </div>
                    )}

                    {/* Pagination */}
                    {events.links.length > 3 && (
                        <div className="flex justify-center gap-2 mt-8">
                            {events.links.map((link, index) => (
                                <Link
                                    key={index}
                                    href={link.url || '#'}
                                    preserveState
                                    preserveScroll
                                    className={`px-5 py-3 rounded-lg transition-all duration-200 font-semibold ${
                                        link.active
                                            ? 'bg-cyan-400 text-gray-900 shadow-xl'
                                            : 'bg-slate-700 text-gray-300 hover:bg-slate-600'
                                    } ${!link.url && 'opacity-50 cursor-not-allowed'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
