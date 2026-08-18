import React, { useState, useMemo } from 'react';

export default function ParcelTable({ parcels, getStatusBadge, formatPrice, urls }) {
    const [search, setSearch] = useState('');
    const [sortKey, setSortKey] = useState('updated_at');
    const [sortDir, setSortDir] = useState('desc');

    const toggleSort = (key) => {
        if (sortKey === key) {
            setSortDir(d => d === 'asc' ? 'desc' : 'asc');
        } else {
            setSortKey(key);
            setSortDir('asc');
        }
    };

    const filtered = useMemo(() => {
        let list = [...parcels];
        if (search) {
            const q = search.toLowerCase();
            list = list.filter(p =>
                (p.tracking_id || '').toLowerCase().includes(q) ||
                (p.customer_name || '').toLowerCase().includes(q) ||
                (p.customer_phone || '').includes(q)
            );
        }
        list.sort((a, b) => {
            let va = a[sortKey] ?? '';
            let vb = b[sortKey] ?? '';
            if (sortKey === 'cash_collection' || sortKey === 'delivery_charge') {
                va = parseFloat(va) || 0;
                vb = parseFloat(vb) || 0;
            }
            if (va < vb) return sortDir === 'asc' ? -1 : 1;
            if (va > vb) return sortDir === 'asc' ? 1 : -1;
            return 0;
        });
        return list;
    }, [parcels, search, sortKey, sortDir]);

    const SortArrow = ({ col }) => (
        <span className={`ml-1 text-[10px] ${sortKey === col ? 'text-wc-primary' : 'opacity-30'}`}>
            <i className={`fas fa-sort-${sortKey === col ? (sortDir === 'asc' ? 'up' : 'down') : 'up'}`}></i>
        </span>
    );

    return (
        <div className="wc-card overflow-hidden h-full">
            <div className="wc-card-header justify-between gap-4 flex-wrap">
                <div className="flex items-center gap-3">
<div className="wc-card-icon bg-[#ecfdf5] text-[#059669]">
                    <i className="fas fa-boxes"></i>
                </div>
                    <div>
                        <h3 className="wc-card-title">Derniers colis</h3>
                        <p className="text-[12px] text-wc-muted m-0">Les 5 colis les plus récents</p>
                    </div>
                </div>
                <div className="flex items-center gap-2.5">
                    <div className="relative hidden sm:block">
                        <i className="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-wc-muted-2 text-[13px] pointer-events-none"></i>
                        <input
                            type="text"
                            placeholder="Rechercher..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            className="wc-input !w-[180px] pl-9 !py-2"
                        />
                    </div>
                    <a href={urls.parcelIndex} className="wc-btn wc-btn-ghost wc-btn-sm">
                        Voir tout <i className="fas fa-arrow-right text-[11px]"></i>
                    </a>
                </div>
            </div>

            {parcels.length === 0 ? (
                <div className="text-center py-14 px-6">
                    <i className="fas fa-inbox text-[34px] mb-3 block" style={{ color: '#cbd5e1' }}></i>
                    <p className="text-[14.5px] font-bold text-wc-ink m-0">Aucun colis pour le moment</p>
                    <p className="text-[13px] text-wc-muted m-0 mt-1">Vos derniers colis apparaîtront ici.</p>
                </div>
            ) : (
                <div className="wc-table-wrap">
                    <table className="wc-table">
                        <thead>
                            <tr>
                                <th className="wc-sortable" onClick={() => toggleSort('tracking_id')}>
                                    Référence <SortArrow col="tracking_id" />
                                </th>
                                <th className="wc-sortable" onClick={() => toggleSort('customer_name')}>
                                    Destinataire <SortArrow col="customer_name" />
                                </th>
                                <th className="wc-sortable text-right" onClick={() => toggleSort('cash_collection')}>
                                    Montant <SortArrow col="cash_collection" />
                                </th>
                                <th className="wc-sortable" onClick={() => toggleSort('status')}>
                                    Statut <SortArrow col="status" />
                                </th>
                                <th className="text-right">Mis à jour</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filtered.map((p, i) => {
                                const badge = getStatusBadge(p.status);
                                return (
                                    <tr key={p.id} style={{ animationDelay: `${i * 0.04}s` }} className="animate-wcRowIn">
                                        <td data-label="Référence">
                                            <span className="font-bold wc-tabular text-[13.5px]" style={{ color: '#059669' }}>
                                                {p.tracking_id}
                                            </span>
                                        </td>
                                        <td data-label="Destinataire">
                                            <div className="flex items-center gap-2.5">
                                                <div className="wcp-av">
                                                    {(p.customer_name || '?')[0].toUpperCase()}
                                                </div>
                                                <div className="min-w-0">
                                                    <div className="font-semibold text-wc-ink truncate max-w-[160px]">
                                                        {p.customer_name || '—'}
                                                    </div>
                                                    <div className="text-[12px] text-wc-muted-2 wc-tabular">
                                                        {p.customer_phone || '—'}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Montant" className="text-right">
                                            <span className="font-bold text-wc-ink wc-tabular">
                                                {formatPrice(p.cash_collection)}
                                            </span>
                                        </td>
                                        <td data-label="Statut">
                                            <span className={`wc-badge wc-badge-${badge.color}`}>
                                                {badge.label}
                                            </span>
                                        </td>
                                        <td data-label="Mis à jour" className="text-[12.5px] text-wc-muted whitespace-nowrap text-right">
                                            {p.updated_at || p.created_at}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}