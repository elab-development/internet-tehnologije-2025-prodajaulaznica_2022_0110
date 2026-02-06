import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function MyTickets({ auth, tickets }) {
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
            standard: 'Standard',
            premium: 'Premium',
            vip: 'VIP',
        };
        return labels[name] || name;
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Moje ulaznice" />

            <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 py-8">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-bold text-white mb-8">Moje ulaznice</h1>

                    {tickets.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {tickets.map((ticket) => (
                                <div
                                    key={ticket.id}
                                    className="bg-slate-800 border-2 border-slate-700 rounded-2xl p-6 hover:border-cyan-400 transition-all duration-300"
                                >
                                    <div className="flex justify-between items-start mb-4">
                                        <span className={`px-3 py-1 rounded-full text-xs font-bold ${
                                            ticket.status === 'active' ? 'bg-green-500 text-white' :
                                            ticket.status === 'used' ? 'bg-gray-500 text-white' :
                                            'bg-red-500 text-white'
                                        }`}>
                                            {ticket.status === 'active' ? 'Aktivna' :
                                             ticket.status === 'used' ? 'Iskorišćena' : 'Otkazana'}
                                        </span>
                                        <span className="text-cyan-400 font-mono text-sm">
                                            {ticket.unique_code}
                                        </span>
                                    </div>

                                    <h3 className="text-xl font-bold text-white mb-2">
                                        {ticket.event.title}
                                    </h3>

                                    <div className="text-gray-400 text-sm space-y-2 mb-4">
                                        <p>{getTicketTypeLabel(ticket.ticket_type.name)}</p>
                                        <p>{ticket.event.location}</p>
                                        <p>{formatDate(ticket.event.date_start)}</p>
                                    </div>

                                    <div className="pt-4 border-t border-slate-700">
                                        <p className="text-2xl font-bold text-cyan-400">
                                            {ticket.price.toLocaleString()} RSD
                                        </p>
                                        <p className="text-xs text-gray-500 mt-1">
                                            Kupljeno: {formatDate(ticket.purchased_at)}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-slate-800 rounded-2xl border border-slate-700">
                            <div className="text-6xl mb-4"></div>
                            <p className="text-gray-400 text-xl">
                                Nemate kupljenih ulaznica
                            </p>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
