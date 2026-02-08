import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function ShowEvent({ auth, event }) {
    const [quantities, setQuantities] = useState({
        standard: 0,
        premium: 0,
        vip: 0,
    });

    const [editingPrice, setEditingPrice] = useState(null);
    const [priceForm, setPriceForm] = useState({ price: 0, capacity: 0 });

    const isAdmin = auth.user?.role === 'admin';

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('sr-RS', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const getTicketType = (name) => {
        return event.ticket_types?.find(t => t.name === name);
    };

    const handleQuantityChange = (type, value) => {
        const qty = Math.max(0, parseInt(value) || 0);
        setQuantities({ ...quantities, [type]: qty });
    };

    const calculateTotal = () => {
        let total = 0;
        Object.keys(quantities).forEach(type => {
            const ticketType = getTicketType(type);
            if (ticketType) {
                total += ticketType.price * quantities[type];
            }
        });
        return total;
    };

    const handlePurchase = () => {
        const tickets = [];

        Object.keys(quantities).forEach(type => {
            const ticketType = getTicketType(type);
            if (quantities[type] > 0 && ticketType) {
                tickets.push({
                    ticket_type_id: ticketType.id,
                    quantity: quantities[type],
                });
            }
        });

        if (tickets.length === 0) {
            alert('Morate izabrati najmanje jednu kartu!');
            return;
        }

        router.post('/orders', {
            event_id: event.id,
            tickets: tickets,
        });
    };

    const startEditPrice = (ticketType) => {
        setEditingPrice(ticketType.id);
        setPriceForm({ price: ticketType.price, capacity: ticketType.capacity });
    };

    const savePrice = (ticketTypeId) => {
        router.put(`/ticket-types/${ticketTypeId}`, priceForm, {
            preserveScroll: true,
            onSuccess: () => {
                setEditingPrice(null);
            },
        });
    };

    const renderTicketCard = (type, label, emoji) => {
        const ticketType = getTicketType(type);
        if (!ticketType) return null;

        const available = ticketType.capacity - ticketType.sold;
        const isEditing = editingPrice === ticketType.id;

        return (
            <div className="bg-slate-700 border-2 border-slate-600 rounded-2xl p-6 hover:border-cyan-400 transition-all duration-300">
                <div className="flex items-center justify-between mb-4">
                    <h3 className="text-2xl font-bold text-white flex items-center gap-2">
                        {emoji} {label}
                    </h3>
                    <span className={`px-3 py-1 rounded-full text-xs font-bold ${
                        available > 0 ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                    }`}>
                        {available > 0 ? `${available} dostupno` : 'Rasprodato'}
                    </span>
                </div>

                {isEditing && isAdmin ? (
                    <div className="space-y-3 mb-4">
                        <div>
                            <label className="text-cyan-400 text-sm">Cena (RSD)</label>
                            <input
                                type="number"
                                value={priceForm.price}
                                onChange={(e) => setPriceForm({ ...priceForm, price: e.target.value })}
                                className="w-full mt-1 px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white"
                            />
                        </div>
                        <div>
                            <label className="text-cyan-400 text-sm">Kapacitet</label>
                            <input
                                type="number"
                                value={priceForm.capacity}
                                onChange={(e) => setPriceForm({ ...priceForm, capacity: e.target.value })}
                                className="w-full mt-1 px-3 py-2 bg-slate-600 border border-slate-500 rounded-lg text-white"
                            />
                        </div>
                        <div className="flex gap-2">
                            <button
                                onClick={() => savePrice(ticketType.id)}
                                className="flex-1 px-4 py-2 bg-cyan-400 text-gray-900 rounded-lg font-bold hover:bg-cyan-500"
                            >
                                Sačuvaj
                            </button>
                            <button
                                onClick={() => setEditingPrice(null)}
                                className="px-4 py-2 bg-slate-600 text-white rounded-lg hover:bg-slate-500"
                            >
                                Otkaži
                            </button>
                        </div>
                    </div>
                ) : (
                    <>
                        <div className="text-3xl font-bold text-cyan-400 mb-4">
                            {ticketType.price.toLocaleString()} RSD
                        </div>

                        {isAdmin ? (
                            <button
                                onClick={() => startEditPrice(ticketType)}
                                className="w-full px-4 py-3 bg-yellow-500 text-white rounded-lg font-bold hover:bg-yellow-600 transition"
                            >
                                ✏️ Izmeni cenu
                            </button>
                        ) : available > 0 ? (
                            <div>
                                <label className="text-gray-400 text-sm mb-2 block">Količina</label>
                                <input
                                    type="number"
                                    min="0"
                                    max={available}
                                    value={quantities[type]}
                                    onChange={(e) => handleQuantityChange(type, e.target.value)}
                                    className="w-full px-4 py-3 bg-slate-600 border-2 border-slate-500 rounded-lg text-white text-center text-xl focus:border-cyan-400 focus:outline-none"
                                />
                            </div>
                        ) : (
                            <div className="text-center py-3 bg-red-500/20 text-red-400 rounded-lg font-bold">
                                Rasprodato
                            </div>
                        )}
                    </>
                )}
            </div>
        );
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title={event.title} />

            <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 py-8">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {/* Back Button */}
                    <Link
                        href="/dashboard"
                        className="inline-flex items-center gap-2 text-cyan-400 hover:text-cyan-300 mb-6 transition"
                    >
                        ← Nazad na Dashboard
                    </Link>

                    <div className="bg-slate-800 rounded-2xl p-8 mb-8 border border-slate-700">
                        <div className="mb-4">
                            <span className="inline-block px-4 py-1 text-sm font-bold rounded-full bg-cyan-400 text-gray-900">
                                {event.category.name}
                            </span>
                        </div>

                        <h1 className="text-5xl font-bold text-white mb-4">{event.title}</h1>

                        <p className="text-gray-400 text-lg mb-6">{event.description}</p>

                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-gray-300">
                            <div className="flex items-center gap-3">
                                <span>Lokacija: {event.location}</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <span>Početak: {formatDate(event.date_start)}</span>
                            </div>
                            <div className="flex items-center gap-3">
                                <span>Kraj: {formatDate(event.date_end)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Tickets Section */}
                    <h2 className="text-3xl font-bold text-white mb-6">
                        {isAdmin ? 'Upravljanje kartama' : 'Dostupne karte'}
                    </h2>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        {renderTicketCard('standard', 'Standard', '🎫')}
                        {renderTicketCard('premium', 'Premium', '⭐')}
                        {renderTicketCard('vip', 'VIP', '👑')}
                    </div>

                    {/* Purchase Summary (only for non-admin) */}
                    {calculateTotal() > 0 && (
                        <div className="bg-slate-800 rounded-2xl p-8 border-2 border-cyan-400">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-2xl font-bold text-white">Ukupno za plaćanje:</h3>
                                <p className="text-4xl font-bold text-cyan-400">
                                    {calculateTotal().toLocaleString()} RSD
                                </p>
                            </div>

                            <button
                                onClick={handlePurchase}
                                className="w-full px-8 py-4 bg-gradient-to-r from-cyan-400 to-purple-600 text-white rounded-lg font-bold text-xl hover:shadow-2xl hover:scale-105 transition-all duration-300"
                            >
                                Kupi karte
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
