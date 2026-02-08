import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Edit({ auth, mustVerifyEmail, status, tickets, adminStats}) {
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

    const getTicketTypeLabel = (name) => {
        const labels = {
            standard: '🎫 Standard',
            premium: '⭐ Premium',
            vip: '👑 VIP',
        };
        return labels[name] || name;
    };

    const totalSpent = tickets.reduce((sum, ticket) => sum + parseFloat(ticket.price), 0);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Moj profil
                </h2>
            }
        >
            <Head title="Profil" />

            <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 py-8">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {/* Lični podaci */}
                    <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700">
                        <h3 className="text-2xl font-bold text-cyan-400 mb-6">👤 Lični podaci</h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p className="text-sm text-gray-400 mb-1">Ime i prezime</p>
                                <p className="text-lg text-white font-semibold">
                                    {auth.user.name} {auth.user.surname}
                                </p>
                            </div>

                            <div>
                                <p className="text-sm text-gray-400 mb-1">Email</p>
                                <p className="text-lg text-white font-semibold">{auth.user.email}</p>
                            </div>

                            <div>
                                <p className="text-sm text-gray-400 mb-1">Korisničko ime</p>
                                <p className="text-lg text-white font-semibold">{auth.user.username}</p>
                            </div>

                            <div>
                                <p className="text-sm text-gray-400 mb-1">Uloga</p>
                                <p className="text-lg text-white font-semibold capitalize">
                                    {auth.user.role === 'admin' ? '🔑 Administrator' :
                                     auth.user.role === 'moderator' ? '⚙️ Moderator' : '👤 Korisnik'}
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Statistika */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="bg-slate-800 rounded-2xl p-6 border border-slate-700">
                            <p className="text-gray-400 text-sm mb-2">Ukupno ulaznica</p>
                            <p className="text-4xl font-bold text-cyan-400">{tickets.length}</p>
                        </div>

                        <div className="bg-slate-800 rounded-2xl p-6 border border-slate-700">
                            <p className="text-gray-400 text-sm mb-2">Ukupno potrošeno</p>
                            <p className="text-4xl font-bold text-cyan-400">
                                {totalSpent.toLocaleString()} RSD
                            </p>
                        </div>

                        <div className="bg-slate-800 rounded-2xl p-6 border border-slate-700">
                            <p className="text-gray-400 text-sm mb-2">Aktivnih ulaznica</p>
                            <p className="text-4xl font-bold text-cyan-400">
                                {tickets.filter(t => t.status === 'active').length}
                            </p>
                        </div>
                    </div>
                    {/* Admin Stats - Posledjih 5 dana */}
                    {auth.user.role === 'admin' && adminStats && (
                        <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700 mb-6">
                            <h3 className="text-2xl font-bold text-cyan-400 mb-6">📊 Statistika prodaje (posledjih 5 dana)</h3>
                            <div className="space-y-3">
                                {adminStats.map((stat, index) => (
                                    <div key={index} className="flex justify-between items-center p-4 bg-slate-700 rounded-lg">
                                        <span className="text-white font-semibold">
                                            {new Date(stat.date).toLocaleDateString('sr-RS', {
                                                day: '2-digit',
                                                month: 'long',
                                                year: 'numeric'
                                            })}
                                        </span>
                                        <span className="text-2xl font-bold text-cyan-400">
                                            {stat.count} karata
                                        </span>
                                    </div>
                                ))}
                            </div>
                            <div className="mt-6 p-4 bg-slate-700 rounded-lg border-t-4 border-cyan-400">
                                <div className="flex justify-between items-center">
                                    <span className="text-white font-bold text-lg">UKUPNO:</span>
                                    <span className="text-3xl font-bold text-cyan-400">
                                        {adminStats.reduce((sum, stat) => sum + stat.count, 0)} karata
                                    </span>
                                </div>
                            </div>
                        </div>
                    )}

                {/* Ulaznice */}
                {auth.user.role !== 'admin' && (
                <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700">
                    <h3 className="text-2xl font-bold text-cyan-400 mb-6">🎫 Moje ulaznice</h3>

                    {tickets.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {Object.values(
                                tickets.reduce((grouped, ticket) => {
                                    const key = `${ticket.event_id}-${ticket.ticket_type_id}`;

                                    if (!grouped[key]) {
                                        grouped[key] = {
                                            ...ticket,
                                            count: 0,
                                            totalPrice: 0,
                                            codes: []
                                        };
                                    }

                                    grouped[key].count += 1;
                                    grouped[key].totalPrice += parseFloat(ticket.price);
                                    grouped[key].codes.push(ticket.unique_code);

                                    return grouped;
                                }, {})
                            ).map((groupedTicket, index) => (
                                <div
                                    key={index}
                                    className="bg-slate-700 border-2 border-slate-600 rounded-2xl p-6 hover:border-cyan-400 transition-all duration-300"
                                >
                                    <div className="flex justify-between items-start mb-4">
                                        <span className={`px-3 py-1 rounded-full text-xs font-bold ${
                                            groupedTicket.status === 'active' ? 'bg-green-500 text-white' :
                                            groupedTicket.status === 'used' ? 'bg-gray-500 text-white' :
                                            'bg-red-500 text-white'
                                        }`}>
                                            {groupedTicket.status === 'active' ? 'Aktivna' :
                                            groupedTicket.status === 'used' ? 'Iskorišćena' : 'Otkazana'}
                                        </span>
                                        <span className="px-3 py-1 rounded-full text-xs font-bold bg-cyan-400 text-gray-900">
                                            {groupedTicket.count}x {getTicketTypeLabel(groupedTicket.ticket_type.name)}
                                        </span>
                                    </div>

                                    <h4 className="text-xl font-bold text-white mb-2 line-clamp-2">
                                        {groupedTicket.event.title}
                                    </h4>

                                    <div className="text-gray-400 text-sm space-y-2 mb-4">
                                        <p>📍 {groupedTicket.event.location}</p>
                                        <p>📅 {formatDate(groupedTicket.event.date_start)}</p>
                                    </div>

                                    <div className="pt-4 border-t border-slate-600">
                                        <p className="text-2xl font-bold text-cyan-400">
                                            {groupedTicket.totalPrice.toLocaleString()} RSD
                                        </p>
                                        <p className="text-xs text-gray-500 mt-1">
                                            Ukupno za {groupedTicket.count} {groupedTicket.count === 1 ? 'ulaznicu' : 'ulaznice'}
                                        </p>

                                        {/* Kodovi */}
                                        <details className="mt-3">
                                            <summary className="text-sm text-cyan-400 cursor-pointer hover:text-cyan-300">
                                                Prikaži kodove
                                            </summary>
                                            <div className="mt-2 space-y-1">
                                                {groupedTicket.codes.map((code, i) => (
                                                    <p key={i} className="text-xs font-mono text-gray-400">
                                                        {code}
                                                    </p>
                                                ))}
                                            </div>
                                        </details>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12">
                            <div className="text-6xl mb-4">🎫</div>
                            <p className="text-gray-400 text-lg">
                                Nemate kupljenih ulaznica
                            </p>
                        </div>
                    )}

                </div>
                )}
            </div>
        </div>
        </AuthenticatedLayout>
    );
}
